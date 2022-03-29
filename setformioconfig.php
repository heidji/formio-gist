<?php

$filename = '/var/www/html/formioconf.json';

if(isset($_POST['test'])){
    $text = $_POST['test'];
    if($text == '' || $text != '' && json_encode(json_decode($text)) != 'null'){
        file_put_contents($filename, json_encode(json_decode($text, JSON_UNESCAPED_UNICODE), JSON_UNESCAPED_UNICODE));
    }
}

if (file_exists($filename)) {
    $text = json_encode(json_decode(file_get_contents($filename), JSON_UNESCAPED_UNICODE), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
} else {
    $text = '';
}
?>
<form action="/formio/setformioconfig.php" method="post">
    <textarea name="test" id="test" cols="200" rows="50"><?= $text ?></textarea>
    <input type="submit">
</form>
