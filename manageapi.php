<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<?php

$manager = new MongoDB\Driver\Manager(
    'mongodb://localhost:27017'
);

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
            <th scope="col">Bearbeiten</th>
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
