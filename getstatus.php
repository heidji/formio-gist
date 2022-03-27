<?php

use LigaInsider\App\Controllers\Extra;

$input = json_decode(file_get_contents('php://input'));
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

$manager = new MongoDB\Driver\Manager(
    'mongodb+srv://heidji:testproject1@cluster0.srphi.mongodb.net/test?retryWrites=true&w=majority'
);

$filter = ['_id' => $input->id];
$options = [];

$query = new MongoDB\Driver\Query($filter, $options);
$cursor = $manager->executeQuery('db.collection', $query);
$res = $cursor->toArray();

if(count($res) == 0){
    echo json_encode(['code' => 0]);
    exit;
}

foreach ($res as $document) {
    echo json_encode(['code' => 1, 'data' => $document]);
    exit;
}
