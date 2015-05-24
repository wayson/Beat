<?php
/**
 * Created by PhpStorm.
 * User: Wayson
 * Date: 5/24/2015
 * Time: 8:01 PM
 */
require_once 'Model/Lawn.php';
require_once 'Model/Mower.php';
require_once 'Model/Position.php';

require 'db/DB.php';
require 'lib/Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->post('/lawn', function() use ($app){
    $width = $app->request->params('width');
    $height = $app->request->params('height');

    $lawn = array('width' => $width, 'height' => $height);
    $db = new DB();
    $lawnId = $db->insertLawn($lawn);
    $db->closeDB();

    $returnResult = array(
        'id' => $lawnId,
        'width' => $width,
        'height' => $height
    );

    echo json_encode($returnResult);
});
$app->get('/lawn/:id', function($id){
    $db = new DB();
    $lawn = $db->getLawnById($id);
    $db->closeDB();

    if(empty($lawn))
    {
        echo json_encode(array('error_code' => 404, 'message' => 'Lawn not found'));
    }
    else
    {
        echo json_encode($lawn);
    }

});
$app->delete('/lawn/:id', function($id){
    $db = new DB();
    $result = $db->deleteLawnById($id);
    $db->closeDB();

    echo json_encode($result);
});
$app->post('/lawn/:id/mower', function($id) use ($app){
    $mower = array(
        'x' => $app->request->params('x'),
        'y' => $app->request->params('y'),
        'heading' => $app->request->params('heading'),
        'commands' => $app->request->params('commands'),
        'lawn_id' => $id
    );
    $db = new DB();
    $mower_id = $db->insertMower($mower);
    $db->closeDB();

    unset($mower['lawn_id']);
    $mower = array('id' => $mower_id) + $mower;

    echo json_encode($mower);

});
$app->get('/lawn/:id/mower/:mid', function($id, $mid) use ($app){
    $db = new DB();
    $mower = $db->getMowerByIdAndLawnId($mid, $id);
    $db->closeDB();

    echo json_encode($mower);
});
$app->put('/lawn/:id/mower/:mid', function($id, $mid) use ($app){
    $db = new DB();
    $mower = $db->getMowerByIdAndLawnId($mid, $id);
    if(empty($mower))
    {
        echo json_encode(array('error_code' => '404', 'message'=> 'Mower not found'));
    }
    else
    {
        $mower['x'] = $app->request->params('x');
        $mower['y'] = $app->request->params('y');
        $mower['heading'] = $app->request->params('heading');
        $mower['commands'] = $app->request->params('commands');
        $mower['id'] = $mid;
        $mower['lawn_id'] = $id;

        $result = $db->updateMower($mower);
        if($result == true)
        {
            $mower = $db->getMowerByIdAndLawnId($mid, $id);
            $db->closeDB();
            unset($mower['lawn_id']);
            echo json_encode($mower);
        }
        else
        {
            $db->closeDB();
            echo json_encode(array('error_code' => '302', 'message' => 'Unable to update mower'));
        }
    }

});
$app->delete('/lawn/:id/mower/:mid', function($id, $mid){
    $db = new DB();
    $result = $db->deleteMowerByIdAndLawnId($mid, $id);
    $db->closeDB();

    echo json_encode($result);
});
$app->post('/lawn/:id/execute', function($id){
    $db = new DB();
    $lawnResult = $db->getLawnById($id);

    if(empty($lawnResult))
    {
        echo json_encode(array('error_code' => '404', 'message' => 'Lawn not found'));
    }
    elseif(empty($lawnResult['mowers']))
    {
        echo json_encode(array('error_code' => '204', 'message' => 'No mower on this lawn'));
    }
    else
    {
        $Lawn = new Lawn($lawnResult['width'], $lawnResult['height']);
        foreach($lawnResult['mowers'] as $mower)
        {
            $position = new Position($mower['x'], $mower['y'], $mower['heading']);
            $mowerObj = new Mower($position, $mower['commands']);
            $Lawn->addMover($mowerObj);
        }

        $result = $Lawn->runMovers();

        if($result['process_result'] == true)
        {
            $mowers = $Lawn->getMowers();

            for($i = 0; $i < count($lawnResult['mowers']); $i++)
            {
                $position = $mowers[$i]->getCurrentPosition();
                $lawnResult['mowers'][$i]['x'] = $position->getCordX();
                $lawnResult['mowers'][$i]['y'] = $position->getCordY();
                $lawnResult['mowers'][$i]['heading'] = $position->getOrientation();
                unset($lawnResult['mowers'][$i]['lawn_id']);
            }
            echo json_encode($lawnResult);
        }
        else
        {
            echo $result['error_message'];
        }
    }
});
$app->run();



