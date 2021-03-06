<?php
$page_title = 'Sprachtexte konfigurieren';
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

$filename = $dir.'/languages.json';

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
<div class="container" style="padding: 20px;">
    <form action="/formio/setlanguages.php" method="post">
        <div class="mb-3">
            <textarea name="test" id="test" cols="200" rows="15"><?= $text ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<div class="container" style="background: cadetblue; padding: 20px;">
    Beispiel:
    <div class="container" style="background: white; padding: 0">
        <pre>
            <?=
            htmlspecialchars('
{
	"ua": {
		"Vorname": "ім\'я",
		"Familienname": "прізвище"
	},
	"ru": {
		"Vorname": "Имя",
		"Familienname": "Фамилия"
	},
	"en": {
		"Vorname": "First Name",
		"Familienname": "Last Name"
	}
}
            ');
            ?>
        </pre>
    </div>
</div>
