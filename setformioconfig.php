<?php
$page_title = 'Formfelder konfigurieren';
require_once ('inc/login.php');
require_once ('inc/navbar.php');
?>
<?php

function createPath($path)
{
    if (is_dir($path)) return true;
    $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1);
    $return = createPath($prev_path);
    return ($return && is_writable($prev_path)) ? mkdir($path) : false;
}

$dir = getcwd().'/conf';
createPath($dir);

$filename = $dir.'/formioconf.json';

if (isset($_POST['test'])) {
    $text = $_POST['test'];
    if ($text == '' || $text != '' && json_encode(json_decode($text)) != 'null') {
        file_put_contents($filename, json_encode(json_decode($text, JSON_UNESCAPED_UNICODE), JSON_UNESCAPED_UNICODE));
    }
}

if (file_exists($filename)) {
    $text = json_encode(json_decode(file_get_contents($filename), JSON_UNESCAPED_UNICODE), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    $text = '';
}
?>
<div class="container" style="padding: 20px;">
    <form action="/formio/setformioconfig.php" method="post">
        <div class="mb-3">
            <textarea name="test" id="test" cols="200" rows="30"><?= $text ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<div class="container" style="background: cadetblue; padding: 20px;">
    Emded code:
    <div class="container" style="background: white">
        <pre>
            <?=
            htmlspecialchars('<div id="formio"></div>
            <script type="text/javascript" src="embed.js"></script>');
            ?>
        </pre>
    </div>
</div>
