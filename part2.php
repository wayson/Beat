<?php
/**
 * Created by PhpStorm.
 * User: Wayson
 * Date: 5/22/2015
 * Time: 11:05 PM
 */

require_once '/Model/Lawn.php';

$input = $_POST['input_data'];

$input = trim($input);

$input_arr = explode(' ', $input);

$lawn = new Lawn($input_arr[0], $input_arr[1]);
$result = $lawn->optimizeMoversWithNumber($input_arr[2]);

var_dump($result);
$paths = $lawn->getAllMoverPathsInArray();

var_dump($paths);

