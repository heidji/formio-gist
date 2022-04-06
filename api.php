<?php
require_once('inc/func.php');

header('Content-Type: application/json');
$headers = getallheaders();

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
    // clean options / parents cannot be selected when children have been specified
    $clean = [];
    foreach($options['projection'] as $key => $dummy){
        $parts = explode('.', $key);
        array_pop($parts);
        if(count($parts) > 0){
            foreach($options['projection'] as $key1 => $dummy1){
                if(implode('.', $parts) == $key1)
                    $clean[] = $key1;
            }
        }
    }
    foreach($clean as $item){
        unset($options['projection'][$item]);
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
