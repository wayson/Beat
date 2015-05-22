<?php
/**
 * Created by PhpStorm.
 * User: Wayson
 * Date: 5/22/2015
 * Time: 11:42 AM
 */

require_once 'Mower.php';
require_once 'Position.php';

class Lawn
{
    private $maxCordX;
    private $maxCordY;
    private $movers;

    private $longestPathLength;

    public function __construct($cordX, $cordY)
    {
        $this->maxCordX = $cordX;
        $this->maxCordY = $cordY;
        $this->movers = array();
        $this->longestPathLength = 0;
    }

    public function addMover(Mower $mover)
    {
        $this->movers[] = $mover;

        if($mover->getPathLength() > $this->longestPathLength)
        {
            $this->longestPathLength = $mover->getPathLength();
        }
    }

    public function getAllMoverLastPositionInArray()
    {
        $mover_last_positions = array();

        foreach($this->movers as $mover)
        {
            $mover_last_positions[] = $mover->getCurrentPosition()->toString();
        }

        return $mover_last_positions;
    }

    /**
     * Run all the mowers
     */
    public function runMovers()
    {
        $result = array(
            'process_result' => true,
            'error_message' => ''
        );
        for($i = 0; $i < $this->longestPathLength; $i++)
        {
            if($i != 0 && count($this->movers) > 1)
            {   //if it is not the first step, then we will need to check if any mower has bump into each other

                if($this->anyMoversInValidPosition() == false)
                {
                    $result['process_result'] = false;
                    $result['error_message'] = 'Invalid Mover position in path position: ' . ($i + 1);
                    return $result;
                }
                if($this->noMoverBump() == false)
                {
                    $result['process_result'] = false;
                    $result['error_message'] = 'Mover bumped with each other in path position: ' . ($i+1);
                    return $result;
                }
            }

            foreach($this->movers as $mover)
            {
                $mover->moveToNextStep();
            }
        }

        return $result;
    }

    /**
     * Detect any mower bump
     */
    private function noMoverBump()
    {
        if(count($this->movers) > 1)
        {   //if it is more than one mower
            for($i =0 ; $i < count($this->movers); $i++)
            {
                for($j = $i + 1; $j < count($this->movers); $j++)
                {
                    if(Position::isSamePosition($this->movers[$i]->getPreviousPosition(), $this->movers[$j]->getCurrentPosition()) == true &&
                        Position::isSamePosition($this->movers[$j]->getPreviousPosition(), $this->movers[$i]->getCurrentPosition()) == true)
                    {   //detect if two mowers are moving to others' previous positions
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Check if any mower is in the same position, if yes, then return false
     */
    private function anyMoversInValidPosition()
    {
        $position_arr = array();    //to record the positions are recorded
        foreach($this->movers as $mover)
        {
            $currentPosition = $mover->getCurrentPosition();

//            var_dump($this->maxCordX);
//            var_dump($currentPosition->getCordX());exit;
            if($currentPosition->getCordX() > $this->maxCordX ||
                $currentPosition->getCordY() > $this->maxCordY ||
                $currentPosition->getCordX() < 0 ||
                $currentPosition->getCordY() < 0)
            {   //if any of the mower move out the lawn, then we will return error
                return false;
            }

            if(empty($position_arr[$currentPosition->getCordX()]))
            {
                $position_arr[$currentPosition->getCordX()] = array();
//                $position_arr[$currenPosition->getCordX()][$currenPosition->getCordY()] = 1;
            }

            if(empty($position_arr[$currentPosition->getCordX()][$currentPosition->getCordY()]))
            {
                $position_arr[$currentPosition->getCordX()][$currentPosition->getCordY()] = 1;
            }
            else
            {   //if it is not empty, then we will say there are duplicate position the mower will be in
                return false;
            }
        }

        return true;
    }

}