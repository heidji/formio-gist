<?php
header("Access-Control-Allow-Origin: *");
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

if (file_exists($filename)) {
    $res = json_decode(file_get_contents($filename), JSON_UNESCAPED_UNICODE);
} else {
    $res = [];
}
echo json_encode($res, JSON_UNESCAPED_UNICODE);
?>
