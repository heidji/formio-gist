<?php

function createPath($path)
{
    if (is_dir($path)) return true;
    $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1);
    $return = createPath($prev_path);
    return ($return && is_writable($prev_path)) ? mkdir($path) : false;
}

function uuid()
{
    $arr = array_values(unpack('N1a/n4b/N1c', openssl_random_pseudo_bytes(16)));
    $arr[2] = ($arr[2] & 0x0fff) | 0x4000;
    $arr[3] = ($arr[3] & 0x3fff) | 0x8000;
    return vsprintf('%08x%04x%04x%04x%04x%08x', $arr);
}

if(!isset($_FILES['upload'])){
    header('Location: /formio/uploads'.$_GET['form']);
    exit;
}

// We're putting all our files in a directory called images.
$uploaddir = getcwd().'/uploads/';

// The posted data, for reference
$file = $_FILES['upload'];
$name = $_FILES['upload']['name'];

// Get the mime
$getMime = explode('.', $name);
$mime = end($getMime);

//$new_name = mb_strtolower(str_replace(' ', '_', $name));
$new_name = $_POST['name'];

$path = $uploaddir;
createPath($path);

//if(Extra::createPath($path)){

/*$test_name = $new_name;
while (file_exists($path . $test_name)) {
    $test_name = uuid().'-'.$new_name;
}
$new_name = $test_name;*/
if (move_uploaded_file($_FILES['upload']['tmp_name'], $path . $new_name)) {
    echo json_encode(['code' => 1, 'filename' => $new_name]);
} else {
    echo json_encode(['code' => 0]);
}
exit;

