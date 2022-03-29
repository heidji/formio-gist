<?php

$filename = getcwd().'/languages.json';

if (file_exists($filename)) {
    $res = json_decode(file_get_contents($filename), JSON_UNESCAPED_UNICODE);
} else {
    $res = [];
}
echo json_encode($res, JSON_UNESCAPED_UNICODE);
?>
