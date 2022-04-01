<?php
require_once('inc/func.php');

//$filter = ['_id' => $_GET['id']];
$filter = [];
$options = [];
/*$options = [
    'projection' => ['_id' => 0, 'data.dataGrid' => 1]
];*/

$query = new MongoDB\Driver\Query($filter, $options);
$cursor = $manager->executeQuery('db.collection', $query);

$res = array_reverse($cursor->toArray())[0];

/*if(count($res) == 0){
    echo json_encode(['code' => 0]);
    exit;
}*/

/*foreach ($res as $document) {
    //echo '<pre>'.print_r($document, true).'</pre>';
    $test = json_encode(['code' => 1, 'data' => $document], JSON_UNESCAPED_UNICODE);
}
echo $test;*/
echo json_encode(['code' => 1, 'data' => $res], JSON_UNESCAPED_UNICODE);
