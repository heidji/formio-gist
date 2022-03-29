<?php
$manager = new MongoDB\Driver\Manager(
    'mongodb://localhost:27017'
);
$input = (object)[];
$input->id = '01e61424-8ee9-4313-a5c9-716c3c52a154';
$filter = ['_id' => $input->id];
$filter = [];
$options = [];

$query = new MongoDB\Driver\Query($filter, $options);
$cursor = $manager->executeQuery('db.collection', $query);

$res = $cursor->toArray();

if(count($res) == 0){
    echo json_encode(['code' => 0]);
    exit;
}

foreach ($res as $document) {
    //echo '<pre>'.print_r($document, true).'</pre>';
    $test = json_encode(['code' => 1, 'data' => $document], JSON_UNESCAPED_UNICODE);
}
echo $test;
