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
            <textarea name="test" id="test" cols="200" rows="15"><?= $text ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<div class="container" style="background: cadetblue; padding: 20px;">
    Emded code:
    <div class="container" style="background: white; padding: 0">
        <pre style="margin: 0">
            <?=
            htmlspecialchars('
<script src="https://code.jquery.com/jquery-1.12.4.min.js"
        integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.form.io/formiojs/formio.full.min.css">
<script src="https://cdn.form.io/formiojs/formio.full.min.js"></script>

<script>
window.onFormioSubmit = (json) => {
    // callback for submit
}
</script>
<div id="formio"></div>
<script type="text/javascript" src="https://test.kleinanzeigen.mx/formio/embed.js" data-app="https://test.kleinanzeigen.mx/formio"></script>');
            ?>
        </pre>
    </div>
</div>
