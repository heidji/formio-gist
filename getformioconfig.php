<?php

$filename = '/var/www/html/formioconf.json';

if (file_exists($filename)) {
    $res = json_decode(file_get_contents($filename));
} else {
    $res = [];
}
echo json_encode($res);
?>
