<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
require_once('inc/func.php');

if (!isset($_GET['id'])) {
    $status = 'NICHT GEFUNDEN';
}else{

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
            // get ticket status or parent status
            $sql = 'SELECT 
                        ts.name FROM
                    ost_ticket_status ts
                    JOIN 
                    (SELECT
                    if(xt.status_id is not null, xt.status_id, t.status_id) as res
						FROM
					ost_ticket t
                    LEFT JOIN
                    ost_ticket xt
                    on xt.ticket_id = t.ticket_pid
                    and t.ticket_pid is not null
                    where t.number = ?) t
                    on t.res = ts.id
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
