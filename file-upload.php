<?php
session_start();

ini_set('display_errors',1);
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});

include 'config.php';
Security::isAccessApproved();

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$target_dir = "uploads/";
$newFileName = date('Y-m-d-H-i-s') . "_" . str_replace(" ", "", basename($_FILES["file"]["name"]));
$target_file = $target_dir . $newFileName;
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));



if ($_FILES["file"]["error"] == 1) {
    http_response_code(500);
    echo json_encode(array("error" => "Unable to upload file."));
    exit();
}


// Check if file already exists
if (file_exists($target_file)) {
    http_response_code(500);
    echo json_encode(array("error" => "Sorry, file already exists."));
    exit();
}

// Check file size
if ($_FILES["file"]["size"] == 0 || $_FILES["file"]["size"] > Utils::return_bytes(ini_get('upload_max_filesize'))) {
    http_response_code(413);
    echo json_encode(array("error" => "Sorry, your file is too large."));
    exit();
}

// Allow certain file formats
$allowedFileTypes = array("jpg", "png", "jpeg", "gif");
if (!in_array($imageFileType, $allowedFileTypes)) {
    http_response_code(500);
    echo json_encode(array("error" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed. Provide file is ". $imageFileType));
    exit();
}

if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {

    Thumbnailer::createThumbnail($target_file, $target_dir . 'thumbs/' . $newFileName, 250);

    http_response_code(200);
    echo json_encode(array("fileName" => $newFileName));
    exit();
} else {
    http_response_code(500);
    echo json_encode(array("error" => "Unable to upload file"));
    exit();
}


function generateRandomString($length = 10)
{
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}