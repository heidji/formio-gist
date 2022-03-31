<?php
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
            $db = new mysqli("mysql","osticket","secret","osticket");

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
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.form.io/formiojs/formio.full.min.css">
<script src="https://cdn.form.io/formiojs/formio.full.min.js"></script>
<div class="alert alert-success" role="alert">
    STATUS: <?= $status ?>
</div>
<iframe id="formioframe<?= $_GET['id'] ?>" frameborder="0" style="width: 100%" scrolling = "no" src = "/formio/getform.php?id=<?= $_GET['id'] ?>" title = "description"> </iframe> <script> function resizeIframe<?= $_GET['id'] ?>(height) { document.getElementById("formioframe<?= $_GET['id'] ?>").style.height = height + "px" } var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent"; var eventer = window[eventMethod]; var messageEvent = eventMethod === "attachEvent" ? "onmessage" : "message"; eventer(messageEvent, function(e) { if ("formio<?= $_GET['id'] ?>" in e.data) resizeIframe<?= $_GET['id'] ?>(e.data.height); });</script>
