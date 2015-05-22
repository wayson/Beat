<?php
/**
 * Created by PhpStorm.
 * User: Wayson
 * Date: 5/22/2015
 * Time: 10:42 AM
 */

require_once '/Model/Lawn.php';
require_once '/Model/Mower.php';
require_once '/Model/Position.php';

$input = $_POST['input_data'];

$input_arr = explode(PHP_EOL, $input);

$lawn = null;

for($i = 0; $i < count($input_arr); $i++)
{
    $input_arr[$i] = trim($input_arr[$i]);

    if($i == 0)
    {   //read the lawn dimension in the first line
        $value_arr = explode(' ', $input_arr[$i]);
        $lawn = new Lawn($value_arr[0], $value_arr[1]);
    }
    else
    {
        //read the mover position
        $mower_position_arr = explode(' ', $input_arr[$i]);
        $path = $input_arr[++$i];
        $position = new Position($mower_position_arr[0], $mower_position_arr[1], $mower_position_arr[2]);
        $mower = new Mower($position, $path);
        $lawn->addMover($mower);
    }
}

if(!empty($lawn))
{
    $result = $lawn->runMovers();

    var_dump($result);

    $last_positions = $lawn->getAllMoverLastPositionInArray();

    var_dump($last_positions);
}
//
//$mower_position_arr = explode(' ', $input_arr[1]);
//$path = $input_arr[2];
//
//$position = new Position($mower_position_arr[0], $mower_position_arr[1], $mower_position_arr[2]);
//$mower = new Mower($position, $path);
//
//$mower->processWholePath();
//
//var_dump($mower->getCurrentPosition());
