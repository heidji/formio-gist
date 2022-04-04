<?php
require_once ('inc/func.php');
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->delete([], []);
$result = $manager->executeBulkWrite('db.collection', $bulk);
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->delete([], []);
$result = $manager->executeBulkWrite('db.apis', $bulk);exit;
