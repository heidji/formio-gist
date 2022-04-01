<?php
$page_title = 'Endpunkt anlegen/bearbeiten';
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
} elseif (isset($_POST['uri'])) {

    $new = true;
    $filter = ['uri' => $_POST['uri']];
    $options = [];

    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery('db.apis', $query);
    $res = $cursor->toArray();
    if (count($res) > 0)
        $new = false;

    $arr = array_values(unpack('N1a/n4b/N1c', openssl_random_pseudo_bytes(16)));
    $arr[2] = ($arr[2] & 0x0fff) | 0x4000;
    $arr[3] = ($arr[3] & 0x3fff) | 0x8000;
    $id = vsprintf('%08x%04x%04x%04x%04x%08x', $arr);

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
    if ($new)
        $doc['_id'] = $id;

    if ($_POST['uri'] != '')
        $bulk->update(
            [
                'uri' => $_POST['uri'],
            ], $doc, ['upsert' => true]
        );

    $manager->executeBulkWrite('db.apis', $bulk);
    header('Location: /formio/setapi.php?uri=' . $_POST['uri']);
}


?>

<div class="container" style="margin-left: 50px; width: 50%">
    <form action="/formio/setapi.php" method="post">
        <?php foreach ($output as $item): ?>
            <div style="padding-left: <?= count(explode('|', $item[0])) * 30 - 60 ?>px" class="form-check">
                <input <?= isset($projection) && in_array($item[0], array_keys($projection)) ? 'checked' : '' ?>
                        type="checkbox" class="form-check-input" id="elem_<?= $item[0] ?>"
                        name=fields[<?= $item[0] ?>]">
                <label class="form-check-label" for="elem_<?= $item[0] ?>"><?= $item[1] ?></label>
            </div>
        <?php endforeach; ?>
        <div class="form-group">
            <label for="uri">URI</label>
            <input name="uri" type="text" class="form-control" id="uri" placeholder="URI" value="<?= $uri ?? '' ?>">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<div class="container" style="background: cadetblue; padding: 20px;">
    Endpunkt URI:
    <div class="container" style="background: white">
        <pre>
            <?=
            htmlspecialchars("
curl --location --request GET 'https://test.kleinanzeigen.mx/formio/api.php/<URI>' \
    --header 'X-API-KeY: 933f9ebf06b440f59aa47b2b01b2d280'
            ");
            ?>
        </pre>
    </div>
</div>
