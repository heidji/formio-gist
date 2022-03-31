<?php

header('Content-Type: application/json');
$headers = getallheaders();

$manager = new MongoDB\Driver\Manager(
    'mongodb://localhost:27017'
);

foreach ($headers as $k => $v){
    if(strtolower($k) == strtolower('X-API-Key')){
        $apikey = $v;
        break;
    }
}
if(!isset($apikey))
    die('no auth');
if(isset($apikey)){
    $parts = explode('/', $_SERVER['REQUEST_URI']);
    foreach($parts as $k => $part){
        if($part == 'api.php')
            break;
    }
    if(!isset($parts[$k+1]) || $parts[$k+1] == '')
        die('no auth');
    $uri = $parts[$k+1];

    $filter = ['_id' => $apikey , 'uri' => $uri];
    $options = [];

    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery('db.apis', $query);
    $res = $cursor->toArray();
    if(count($res) == 0)
        die('no auth');
    $data = $res[0];
    $projection = json_decode(json_encode($data->projection, JSON_UNESCAPED_UNICODE), true);

    $json = json_decode(file_get_contents(getcwd() . '/formioconf.json'));
//echo json_encode(json_decode($json), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

    function iterate($node, $output = [], $map = 'data', $iterable_types = ['components', 'columns', 'panel'], $iterable_with_key = ['datagrid'])
    {
        if (isset($node->type) && $node->type == 'button') return $output;
        if (isset($node->key) && !in_array($node->type, $iterable_types))
            $map .= '|' . $node->key;
        if ($map != 'data' && isset($node->type) && !in_array($node->type, $iterable_types) && !in_array($node->type, $iterable_with_key)
            || in_array($node->type, $iterable_with_key) && !in_array($node->type, $output))
            $output[] = [$map, $node->label];
        $next = '';
        if (isset($node->components))
            $next = 'components';
        elseif (in_array($node->type, $iterable_types) || in_array($node->type, $iterable_with_key)) {
            $next = $node->type;
        }
        if ($next != '') {
            foreach ($node->{$next} as $item) {
                $output = iterate($item, $output, $map);
            }
        }
        return $output;
    }
    $output = iterate($json);
//echo '<pre>'.print_r($output, true).'</pre>';

// prep label list
    $labels = [];
    foreach($output as $item){
        $labels[end(explode('|', $item[0]))] = $item[1];
    }

    $options = ['projection' => []];
    foreach ($projection as $key => $dummy) {
        $options['projection'][str_replace('|', '.', $key)] = 1;
    }
    $query = new MongoDB\Driver\Query([], $options);
    $cursor = $manager->executeQuery('db.collection', $query);

    $res = $cursor->toArray();
    $return = [];
    foreach($res as $item){
        $return[] = $item->data;
    }

    $keys = array_map('strlen', array_keys($labels));
    array_multisort($keys, SORT_DESC, $labels);

    $res_temp = json_encode($res, JSON_UNESCAPED_UNICODE);
    $find = array_map(fn ($a) => '["'.$a.'"]', array_keys($labels));
    $replace = array_map(fn ($a) => '["'.$a.'"]', array_values($labels));
    $res_temp = str_replace(array_keys($labels), array_values($labels), $res_temp);

    //echo '<pre>'.print_r([array_keys($labels), array_values($labels)], true).'</pre>';exit;
    $res = json_decode($res_temp, JSON_UNESCAPED_UNICODE);

    $return = [];
    foreach($res as $item){
        if($item['data'] != null)
            $return[] = ['id' => $item['_id'], 'data' => $item['data']];
    }

    echo json_encode($return, JSON_UNESCAPED_UNICODE);
}
?>
