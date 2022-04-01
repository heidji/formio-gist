<?php
$page_title = 'Suche';
require_once('inc/login.php');
require_once('inc/navbar.php');
?>
<?php

$json = json_decode(file_get_contents(getcwd() . '/conf/formioconf.json'));
//echo json_encode(json_decode($json), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

function iterate($node, $output = [], $map = 'data')
{
    $types = [
        'form' => [
            'map_append' => false,
            'iterable_node' => 'components'
        ],
        'components' => [
            'map_append' => false,
            'iterable_node' => 'components'
        ],
        'columns' => [
            'map_append' => false,
            'iterable_node' => 'columns'
        ],
        'panel' => [
            'map_append' => false,
            'iterable_node' => 'components'
        ],
        'table' => [
            'map_append' => false,
            'iterable_node' => 'rows'
        ],
        'fieldset' => [
            'map_append' => false,
            'iterable_node' => 'components'
        ],
        'datagrid' => [
            'map_append' => 'key',
            'iterable_node' => 'components'
        ],
        'container' => [
            'map_append' => 'key',
            'iterable_node' => 'components'
        ],
        'datamap' => [
            'map_append' => 'key',
            'iterable_node' => 'valueComponent'
        ],
        'tabs' => [
            'map_append' => false,
            'iterable_node' => 'components'
        ],
        'selectboxes' => [
            'map_append' => 'key',
            'iterable_node' => 'values'
        ],
        'well' => [
            'map_append' => false,
            'iterable_node' => 'components'
        ],
    ];

    $ignore_types = ['button'];
    $t_map = $map;

    if (isset($node->type) && in_array($node->type, $ignore_types) || empty($node)) return $output;

    if (isset($node->key) && isset($types[$node->type])) {
        if ($types[$node->type]['map_append'] !== false) {
            $map .= '|' . $node->{$types[$node->type]['map_append']};
        }
    }elseif(isset($node->key) && isset($node->type) && !isset($ignore_types[$node->type])){
        $map .= '|' . $node->key;
    }elseif(!isset($node->key) && isset($node->value)){
        $map .= '|' . $node->value;
    }

    if ($map != 'data' && $t_map !== $map)
        $output[] = [$map, $node->label];

    if(is_array($node)){
        foreach ($node as $item) {
            $output = iterate($item, $output, $map);
        }
    }else{
        if(isset($types[$node->type]) && $types[$node->type]['iterable_node'] !== false){
            foreach ($node->{$types[$node->type]['iterable_node']} as $item) {
                $output = iterate($item, $output, $map);
            }
        }elseif(isset($node->components)){
            foreach ($node->components as $item) {
                $output = iterate($item, $output, $map);
            }
        }else{
            return $output;
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

if (isset($_POST['checks'])) {
    $manager = new MongoDB\Driver\Manager(
        'mongodb://localhost:27017'
    );
    $filter = [];
    $options = [
        'projection' => []
    ];
    foreach ($_POST['checks'] as $key => $dummy) {
        $options['projection'][str_replace('|', '.', $key)] = 1;
    }
    foreach ($_POST['fields'] as $k => $v) {
        if($v != '')
            $filter[str_replace('|', '.', $k)] = $v;
    }
    // clean options / parents cannot be selected when children have been specified
    $clean = [];
    foreach($options['projection'] as $key => $dummy){
        $parts = explode('.', $key);
        array_pop($parts);
        if(count($parts) > 0){
            foreach($options['projection'] as $key1 => $dummy1){
                if(implode('.', $parts) == $key1)
                    $clean[] = $key1;
            }
        }
    }
    foreach($clean as $item){
        unset($options['projection'][$item]);
    }
    $options['projection']['_id'] = 1;
    //echo '<pre>' . print_r($options, true) . '</pre>';
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery('db.collection', $query);

    $res = $cursor->toArray();
    $return = [];
    foreach($res as $item){
        $return[] = $item->data;
    }

    $keys = array_map('strlen', array_keys($labels));
    array_multisort($keys, SORT_DESC, $labels);

    $res_temp = json_encode($res, JSON_UNESCAPED_UNICODE);
    $find = array_map(fn ($a) => '["'.$a.'"]', array_keys($labels));
    $replace = array_map(fn ($a) => '["'.$a.'"]', array_values($labels));
    $res_temp = str_replace(array_keys($labels), array_values($labels), $res_temp);

    //echo '<pre>'.print_r([array_keys($labels), array_values($labels)], true).'</pre>';exit;
    $res = json_decode($res_temp, JSON_UNESCAPED_UNICODE);

    $return = [];
    foreach($res as $item){
        $temp = [];
        $temp['_id'] = $item['_id'];
        $temp = array_merge($temp, $item['data']);
        $return[] = $temp;
    }

    //echo json_encode(['code' => 1, 'data' => $res], JSON_UNESCAPED_UNICODE);
    //echo '<pre>'.print_r($res, true).'</pre>';
}

$uploaddir = getcwd().'/uploads/';

?>

    <form action="/formio/search.php" method="post">
        <table>
        <?php foreach ($output as $item): ?>
        <tr>
            <td>
                <div style="padding-left: <?= count(explode('|', $item[0])) * 30 ?>px" class="form-check">
                    <input <?= isset($_POST['checks']) && in_array($item[0], array_keys($_POST['checks'])) ? 'checked' : '' ?>
                            class="form-check-input" id="elem_<?= $item[0] ?>" name="checks[<?= $item[0] ?>]" type="checkbox"/>
                    <label class="form-check-label" for="elem_<?= $item[0] ?>"><?= $item[1] ?></label>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input class="form-control" name="fields[<?= $item[0] ?>]" value="<?= $_POST['fields'][$item[0]] ?? '' ?>" type="text"/>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </table>
        <input type="submit">
    </form>
<?php
// analyze data
$fields = [];
foreach($return as $doc){
    foreach($doc as $k => $v){
        if(!is_array($v))
            $fields[$k] = 1;
    }
}
?>
<?php if(isset($return)): ?>
<table class="table">
    <thead>
    <tr>
        <?php foreach ($fields as $field => $i): ?>
        <th scope="col"><?= $field ?></th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($return as $result): ?>
    <tr>
        <?php foreach ($fields as $field => $i): ?>
        <td><?= $result[$field] ?? '' ?></td>
        <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
