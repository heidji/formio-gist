<?php
$page_title = 'Pull API Endpunkte';
require_once ('inc/login.php');
require_once ('inc/navbar.php');
require_once('inc/func.php');
?>
<?php

$filter = [];
$options = [];

$query = new MongoDB\Driver\Query($filter, $options);
$cursor = $manager->executeQuery('db.apis', $query);
$res = $cursor->toArray();
?>
<div class="container" style="margin-top: 100px;max-width: 70%">
    <table class="table">
        <thead>
        <tr>
            <th scope="col">URI</th>
            <th scope="col">API Key</th>
            <th scope="col">Aktiv</th>
            <th scope="col"><a href="/formio/setapi.php">Endpunkt anlegen</a></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($res as $l): ?>
        <tr>
            <th><?= $l->uri ?></th>
            <td><?= $l->_id ?></td>
            <td><?= $l->active ?></td>
            <td><a href="/formio/setapi.php?uri=<?= $l->uri ?>">Bearbeiten</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
