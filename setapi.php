<?php

$json = json_decode(file_get_contents(getcwd() . '/conf/formioconf.json'));
//echo json_encode(json_decode($json), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

function iterate($node, $output = [], $map = 'data', $iterable_types = ['components', 'columns', 'panel'], $iterable_with_key = ['datagrid'])
{
    if (isset($node->type) && $node->type == 'button') return $output;
    if (isset($node->key) && !in_array($node->type, $iterable_types))
        $map .= '|' . $node->key;
    if ($map != 'data' && isset($node->type) && (!in_array($node->type, $iterable_types) && !in_array($node->type, $iterable_with_key)
        || in_array($node->type, $iterable_with_key) && !in_array($node->type, $output)))
        $output[] = [$map, $node->label];
    $next = '';
    if (isset($node->components))
        $next = 'components';
    elseif (in_array($node->type, $iterable_types) || in_array($node->type, $iterable_with_key)) {
        $next = $node->type;
    }
    if ($next != '') {
        foreach ($node->{$next} as $item) {
            $output = iterate($item, $output, $map);
        }
    }
    return $output;
}

$output = iterate($json);
//echo '<pre>'.print_r($output, true).'</pre>';

$manager = new MongoDB\Driver\Manager(
    'mongodb://localhost:27017'
);

/*$bulk = new MongoDB\Driver\BulkWrite;
$bulk->delete([], []);
$result = $manager->executeBulkWrite('db.apis', $bulk);exit;*/

if (isset($_GET['uri'])) {
    $uri = $_GET['uri'];

    $filter = ['uri' => $_GET['uri']];
    $options = [];

    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery('db.apis', $query);
    $data = $cursor->toArray()[0];
    $projection = json_decode(json_encode($data->projection, JSON_UNESCAPED_UNICODE), true);
}elseif (isset($_POST['uri'])) {
    $uri = $_POST['uri'];

    $projection = ['_id' => 0, 'forsurenoonewilleverpickthisfieldname1235454' => 1];
    foreach ($_POST['fields'] as $key => $dummy) {
        $projection[$key] = 1;
    }
// clean options / parents cannot be selected when children have been specified
    $clean = [];
    foreach ($projection as $key => $dummy) {
        $parts = explode('.', $key);
        array_pop($parts);
        if (count($parts) > 0) {
            foreach ($projection as $key1 => $dummy1) {
                if (implode('.', $parts) == $key1)
                    $clean[] = $key1;
            }
        }
    }
    foreach ($clean as $item) {
        unset($projection[$item]);
    }

    $manager = new MongoDB\Driver\Manager(
        'mongodb://localhost:27017'
    );

    $bulk = new MongoDB\Driver\BulkWrite;
    $doc = [
        'uri' => $uri,
        'active' => true,
        'projection' => $projection
    ];

        $bulk->update(
            [
                'uri' => $_POST['uri'],
            ], $doc, ['upsert' => true]
        );

    $manager->executeBulkWrite('db.apis', $bulk);
    header('Location: /formio/setapi.php?uri=' . $_POST['uri']);
}


?>

<form action="/formio/setapi.php" method="post">
    <?php foreach ($output as $item): ?>
        <div style="position:relative; height: 20px">
            <div style="display: flex; position: absolute; flex-direction: row; left: <?= count(explode('|', $item[0])) * 10 ?>px">
                <input <?= isset($projection) && in_array($item[0], array_keys($projection)) ? 'checked' : '' ?>
                        name=fields[<?= $item[0] ?>]" type="checkbox"/>
                <span><?= $item[1] ?></span>
            </div>
        </div>
    <?php endforeach; ?>
    <label for="uri">URI</label>
    <input id="uri" type="text" name="uri" value="<?= isset($data) ? $data->uri : '' ?>">
    <input type="submit">
</form>
