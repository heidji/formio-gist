<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

if (!isset($_GET['id'])) {
    $status = 'NICHT GEFUNDEN';
}else{
    $manager = new MongoDB\Driver\Manager(
        'mongodb://mongodb:27017'
    );

    $filter = ['_id' => $_GET['id']];
    $options = [];

    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery('db.collection', $query);

    $res = $cursor->toArray();

    if(count($res) == 0){
        $status = 'NICHT GEFUNDEN';
    }else{
        foreach ($res as $document) {
            $osticket = $document->osticket;
            break;
        }
        if(!is_numeric($osticket)){
            $status = 'NICHT GEFUNDEN';
        }else{
            $db = new mysqli("localhost","user","password","osticket");

            $sql = 'SELECT 
                        ts.name FROM
                    ost_ticket_status ts
                    JOIN ost_ticket t
                    on t.status_id = ts.id
                    where t.number = ?
                    limit 1';

            $stmt = $db->prepare($sql);
            $stmt->bind_param('i', $osticket);
            $stmt->bind_result($status);
            $stmt->execute();
            $stmt->fetch();
            $stmt->close();

            if(!isset($status))
                $status = 'NICHT GEFUNDEN';
        }
    }
}

echo json_encode(['status' => $status]);
