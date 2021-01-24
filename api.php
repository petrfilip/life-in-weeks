<?php

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$input = json_decode(file_get_contents("php://input"));


$newWeek = new Week();
$newWeek->yearWeek = $input->yearWeek;
$newWeek->mostCommon = $input->mostCommon;
$newWeek->mostImportant = $input->mostImportant;
$newWeek->gallery = $input->gallery;

$weeks = Utils::loadData("./data/liw-week.json", Week::class);
$weeks[$input->yearWeek] = $newWeek;

usort($weeks, function ($a, $b) {
    return $a->yearWeek > $b->yearWeek;
});

$convertedWeeks = json_encode($weeks);

file_put_contents("./data/liw-week.json", $convertedWeeks);

echo json_encode($newWeek);




