<?php
require_once('inc/func.php');

function createPath($path)
{
    if (is_dir($path)) return true;
    $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1);
    $return = createPath($prev_path);
    return ($return && is_writable($prev_path)) ? mkdir($path) : false;
}

$json = json_decode(file_get_contents(getcwd() . '/formioconf.json'));
//echo json_encode(json_decode($json), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

$output = iterate($json);
//echo '<pre>'.print_r($output, true).'</pre>';

// prep label list
$labels = [];
foreach($output as $item){
    $labels[end(explode('|', $item[0]))] = $item[1];
}

if (isset($_POST)) {
    $filter = [];
    $options = [
        'projection' => ['_id' => 0, 'forsurenoonewilleverpickthisfieldname1235454' => 1]
    ];
    foreach ($_POST as $key => $dummy) {
        $options['projection'][str_replace('|', '.', $key)] = 1;
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
        $return[] = $item['data'];
    }

    //echo json_encode(['code' => 1, 'data' => $res], JSON_UNESCAPED_UNICODE);
    //echo '<pre>'.print_r($res, true).'</pre>';
}

$uploaddir = getcwd().'/uploads/';

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
    <input type="text" name="_internal_endpoint">
    <input type="submit">
</form>
<?php
echo json_encode($return, JSON_UNESCAPED_UNICODE);
echo '<pre>'.print_r($_POST, true).'</pre>';
echo '<pre>'.print_r($output, true).'</pre>';
