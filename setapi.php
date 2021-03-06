<?php
$page_title = 'Endpunkt anlegen/bearbeiten';
require_once('inc/login.php');
require_once('inc/navbar.php');
require_once('inc/func.php');
?>
<?php
$json = json_decode(file_get_contents(getcwd() . '/conf/formioconf.json'));
//echo json_encode(json_decode($json), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

$output = iterate($json);

//echo '<pre>'.print_r($output, true).'</pre>';

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
<?php if(!$new): ?>
<div class="container" style="background: cadetblue; padding: 20px;">
    Endpunkt URI:
    <div class="container" style="background: white; padding: 0">
        <pre style="margin: 0">
            <?=
            htmlspecialchars("
curl --location --request GET 'https://".getenv('OSTICKET_DOMAIN')."/formio/api.php/".($_GET['uri'] ?? '<URI>')."' \
    --header 'X-API-KEY: ".$data->_id."'
            ");
            ?>
        </pre>
    </div>
</div>
<?php endif; ?>
