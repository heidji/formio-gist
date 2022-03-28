<?php

use LigaInsider\App\Controllers\Extra;

$input = json_decode(file_get_contents('php://input'));
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

/*$ticket = '{
	"fields": {
		"project": {
			"key": "AS"
		},
		"issuetype": {
			"id": "10001"
		},
		"summary": "REST ye merry gentlemen.",
		"description": {
			"type": "doc",
			"version": 1,
			"content": [{
				"type": "paragraph",
				"content": [{
					"type": "text",
					"text": "Form",
                    "marks": [
                        {
                            "type": "link",
                            "attrs": {
                                "href": "https://hossidev.ligainsider.de/api/test10/?id=123-123-4"
                            }
                        }
                    ]
				}]
			}]
		}
	}
}';

$ch = curl_init('https://formtickets.atlassian.net/rest/api/3/issue');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/json']);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_USERPWD, 'rserverbot@gmail.com' . ":" . 'kAYXYHrqE9XECF7b2KnC6419');
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $ticket);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$return = curl_exec($ch);
error_log($return);
curl_close($ch);
Extra::printArray($return);exit;*/

$manager = new MongoDB\Driver\Manager(
    'mongodb://localhost:27017'
);

if (!isset($input->data)) {
    echo json_encode(['code' => 0, 'msg' => 'nodata']);
    exit;
}

/////////////
/// WRITE
/// /////////

/*$bulk = new MongoDB\Driver\BulkWrite;
$document1 = ['title' => 'one'];
$document2 = ['_id' => 'custom ID', 'title' => 'two'];
$document3 = ['_id' => new MongoDB\BSON\ObjectId, 'title' => 'three'];
$_id1 = $bulk->insert($document1);
$_id2 = $bulk->insert($document2);
$_id3 = $bulk->insert($document3);

$result = $manager->executeBulkWrite('test2.one', $bulk);*/

/////////////
// OBJECT IDS
/////////////
/*$oid = new MongoDB\BSON\ObjectId('623f2e56ce26cef05d0963e2');

$filter  = ['_id' => ['$ne' => $oid]];
//$filter = [];
$options = ['sort'=>array('_id'=>-1),'limit'=>3]; # limit -1 from newest to oldest

#constructing the query
$query = new MongoDB\Driver\Query($filter, $options);

#executing
$cursor = $manager->executeQuery('db.collection', $query);

echo "dumping results<br>";
foreach ($cursor as $document) {
    \LigaInsider\App\Controllers\Extra::printArray($document);
}*/

$unique = false;

while (!$unique) {
    $arr = array_values(unpack('N1a/n4b/N1c', openssl_random_pseudo_bytes(16)));
    $arr[2] = ($arr[2] & 0x0fff) | 0x4000;
    $arr[3] = ($arr[3] & 0x3fff) | 0x8000;
    $id = vsprintf('%08x-%04x-%04x-%04x-%04x%08x', $arr);
    $filter = ['_id' => $id];
    $options = [];

    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery('db.collection', $query);
    $res = $cursor->toArray();
    if (count($res) == 0)
        $unique = true;
}



    $input->{'_id'} = $id;
    $document1 = $input;
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->insert($document1);
    $manager->executeBulkWrite('db.collection', $bulk);


echo json_encode(['code' => 1]);
exit;
