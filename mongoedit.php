<?php

$db = new mysqli("localhost","user","password","osticket");

$input = json_decode(file_get_contents('php://input'));
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

$manager = new MongoDB\Driver\Manager(
    'mongodb://mongodb:27017'
);

if (!isset($input->data)) {
    echo json_encode(['code' => 0, 'msg' => 'nodata']);
    exit;
}

$bulk = new MongoDB\Driver\BulkWrite;
$bulk->update(
    ['_id' => $input->_id],
    $input
);
$manager->executeBulkWrite('db.collection', $bulk);

echo json_encode(['code' => 1]);
exit;
