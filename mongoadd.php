<?php

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

$unique = false;

while (!$unique) {
    $arr = array_values(unpack('N1a/n4b/N1c', openssl_random_pseudo_bytes(16)));
    $arr[2] = ($arr[2] & 0x0fff) | 0x4000;
    $arr[3] = ($arr[3] & 0x3fff) | 0x8000;
    $id = vsprintf('%08x%04x%04x%04x%04x%08x', $arr);
    $filter = ['_id' => $id];
    $options = [];

    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery('db.collection', $query);
    $res = $cursor->toArray();
    if (count($res) == 0)
        $unique = true;
}

$body = str_replace('"', '\"', '<iframe id="formioframe" frameborder="0" style="width: 100%" scrolling = "no" src = "/formio/getform.php?id='.$id.'" title = "description"> </iframe> <script> function resizeIframe(height) { document.getElementById("formioframe").style.height = height + "px" } var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent"; var eventer = window[eventMethod]; var messageEvent = eventMethod === "attachEvent" ? "onmessage" : "message"; eventer(messageEvent, function(e) { if ("formio" in e.data) resizeIframe(e.data.height); });</script>');
$ticket = '{"name": "'.($input->data->familienname ?? 'unbekannt').', '.($input->data->vorname ?? 'unbekannt@unbekannt.de').'", "email": "'.($input->data->email ?? 'unbekannt@unbekannt.com').'", '.(isset($input->data->telefon) ? '"phone": "'.$input->data->telefon.'",' : '"phone": "1234567", ').' "notes": "", "subject": "'.$id.'", "message": "", "ip": "127.0.0.1", "topicId": 1 }';

$ch = curl_init('https://'.getenv('OSTICKET_DOMAIN').'/osticket/api/http.php/tickets.json');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-API-Key: 403C6161D19761BD3C67C4724C9F5A02']);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $ticket);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$return = (int)curl_exec($ch);
curl_close($ch);

$db = new mysqli("mysql","osticket","secret","osticket");
$sql = 'SELECT 
            te.id FROM
        ost_thread_entry te
        JOIN ost_thread th
        on th.id = te.thread_id
        JOIN ost_ticket t
        on t.ticket_id = th.object_id
        and th.object_type = "T"
        where t.number = ?
        limit 1';
$stmt = $db->prepare($sql);
$stmt->bind_param('i', $return);
$stmt->bind_result($_id);
$stmt->execute();
$stmt->fetch();
$stmt->close();

$sql = 'UPDATE ost_thread_entry SET body = ?, format = "html" WHERE id = ?';
$stmt = $db->prepare($sql);
$stmt->bind_param('si', $body, $_id);
$stmt->execute();
$stmt->close();

$sql = 'UPDATE ost_thread_entry SET body = REPLACE(body, ?, ?) where id = ?';
$i='\\';$j='';
$stmt = $db->prepare($sql);
$stmt->bind_param('ssi', $i, $j, $_id);
$stmt->execute();
$stmt->close();


if(isset($return)){
    error_log($id);
    $input->{'_id'} = $id;
    $input->osticket = $return;
    $document1 = $input;
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->insert($document1);
    $manager->executeBulkWrite('db.collection', $bulk);
}else{
    echo json_encode(['code' => 0, 'msg' => "Couldn't add record"]);exit;
}


echo json_encode(['code' => 1, 'id' => $id]);
exit;
