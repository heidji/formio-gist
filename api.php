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

    $json = json_decode(file_get_contents(getcwd() . '/conf/formioconf.json'));
//echo json_encode(json_decode($json), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

    function iterate($node, $output = [], $map = 'data')
    {
        $types = [
            'form' => [
                'map_append' => false,
                'iterable_node' => 'components'
            ],
            'components' => [
                'map_append' => false,
                'iterable_node' => 'components'
            ],
            'columns' => [
                'map_append' => false,
                'iterable_node' => 'columns'
            ],
            'panel' => [
                'map_append' => false,
                'iterable_node' => 'components'
            ],
            'table' => [
                'map_append' => false,
                'iterable_node' => 'rows'
            ],
            'fieldset' => [
                'map_append' => false,
                'iterable_node' => 'components'
            ],
            'datagrid' => [
                'map_append' => 'key',
                'iterable_node' => 'components'
            ],
            'container' => [
                'map_append' => 'key',
                'iterable_node' => 'components'
            ],
            'datamap' => [
                'map_append' => 'key',
                'iterable_node' => 'valueComponent'
            ],
            'tabs' => [
                'map_append' => false,
                'iterable_node' => 'components'
            ],
            'selectboxes' => [
                'map_append' => 'key',
                'iterable_node' => 'values'
            ],
            'well' => [
                'map_append' => false,
                'iterable_node' => 'components'
            ],
        ];

        $ignore_types = ['button'];
        $t_map = $map;

        if (isset($node->type) && in_array($node->type, $ignore_types) || empty($node)) return $output;

        if (isset($node->key) && isset($types[$node->type])) {
            if ($types[$node->type]['map_append'] !== false) {
                $map .= '|' . $node->{$types[$node->type]['map_append']};
            }
        }elseif(isset($node->key) && isset($node->type) && !isset($ignore_types[$node->type])){
            $map .= '|' . $node->key;
        }elseif(!isset($node->key) && isset($node->value)){
            $map .= '|' . $node->value;
        }

        if ($map != 'data' && $t_map !== $map)
            $output[] = [$map, $node->label];

        if(is_array($node)){
            foreach ($node as $item) {
                $output = iterate($item, $output, $map);
            }
        }else{
            if(isset($types[$node->type]) && $types[$node->type]['iterable_node'] !== false){
                foreach ($node->{$types[$node->type]['iterable_node']} as $item) {
                    $output = iterate($item, $output, $map);
                }
            }elseif(isset($node->components)){
                foreach ($node->components as $item) {
                    $output = iterate($item, $output, $map);
                }
            }else{
                return $output;
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
