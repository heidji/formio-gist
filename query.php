<?php

$json = json_decode(file_get_contents(getcwd() . '/formioconf.json'));
//echo json_encode(json_decode($json), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

function iterate($node, $output = [], $map = 'data', $iterable_types = ['components', 'columns'], $iterable_with_key = ['datagrid'])
{
    if (isset($node->type) && $node->type == 'button') return $output;
    if (isset($node->key) && !in_array($node->type, $iterable_types))
        $map .= '|' . $node->key;
    if ($map != 'data' && isset($node->type) && !in_array($node->type, $iterable_types) && !in_array($node->type, $iterable_with_key)
        || in_array($node->type, $iterable_with_key) && !in_array($node->type, $output))
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

// prep label list
$labels = [];
foreach($output as $item){
    $labels[end(explode('|', $item[0]))] = $item[1];
}

if (isset($_POST)) {
    $manager = new MongoDB\Driver\Manager(
        'mongodb://localhost:27017'
    );
    $filter = [];
    $options = [
        'projection' => ['_id' => 0]
    ];
    foreach ($_POST as $key => $dummy) {
        $options['projection'][str_replace('|', '.', $key)] = 1;
    }
    echo '<pre>' . print_r($options, true) . '</pre>';

    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery('db.collection', $query);

    $res = $cursor->toArray();

    //echo json_encode(['code' => 1, 'data' => $res], JSON_UNESCAPED_UNICODE);
}
?>

<form action="/formio/query.php" method="post">
    <?php foreach ($output as $item): ?>
        <div style="position:relative; height: 20px">
            <div style="display: flex; position: absolute; flex-direction: row; left: <?= count(explode('|', $item[0])) * 10 ?>px">
                <input <?= isset($_POST) && in_array($item[0], array_keys($_POST)) ? 'checked' : '' ?> name="<?= $item[0] ?>" type="checkbox"/>
                <span><?= $item[1] ?></span>
            </div>
        </div>
    <?php endforeach; ?>
    <input type="submit">
</form>
<?php if(isset($_POST)): ?>
<Table>
    <tr>
        <td></td>
    </tr>
</Table>
<?php endif; ?>
