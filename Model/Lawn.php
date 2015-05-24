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

    private $coordinates;   //store the coordinates we need to process in part 2 for optimization

    public function __construct($cordX, $cordY)
    {
        $this->maxCordX = $cordX;
        $this->maxCordY = $cordY;
        $this->movers = array();
        $this->longestPathLength = 0;
    }

    public function getMowers()
    {
        return $this->movers;
    }

    /**
     * Input the number of movers and we will optimize the movers to travers the lawn
     * @param $numberOfMover    the number of mover we put it in
     */
    public function optimizeMoversWithNumber($numberOfMover)
    {
        $result = array(
            'process_result' => true,
            'error_message' => ''
        );

        if($numberOfMover > ($this->maxCordX + 1) * ($this->maxCordY + 1))
        {
            $result['process_result'] = false;
            $result['error_message'] = 'Error: The number of movers should not greater than the positions in the lawn.';
        }
        elseif($numberOfMover <= 0)
        {
            $result['process_result'] = false;
            $result['error_message'] = 'Error: Invalid mover number';
        }
        else
        {
            $coordinates = $this->initializeCoordinateArray($this->maxCordX, $this->maxCordY);

            $processPositionsArray = array_chunk($coordinates, ceil(count($coordinates) / $numberOfMover));

            foreach($processPositionsArray as $processPositions)
            {
                $mover = new Mower($processPositions);
                $this->movers[] = $mover;
            }
        }
        return $result;
    }

    /**
     * Initialized the coordinate arrays for chunking the positions to movers
     */
    private function initializeCoordinateArray($maxCordX, $maxCordY)
    {
        $coordinates = array();
        //first of all, create an array to store the path of all positions in the lawn
        $forward = true;
        for($x = 0; $x <= $maxCordX; $x++)
        {
            if($forward == true)
            {
                for($y = 0; $y <= $maxCordY; $y++)
                {
                    $position = new Position($x, $y, 'N');
                    if($y == $maxCordY)
                    {
                        $position->turnRight();
                    }
                    $coordinates[] = $position;
                }

                $forward = false;
            }
            else
            {
                for($y = $maxCordY; $y >= 0; $y--)
                {
                    $position = new Position($x, $y, 'S');
                    if($y == 0)
                    {
                        $position->turnLeft();
                    }
                    $coordinates[] = $position;
                }

                $forward = true;
            }
        }

        return $coordinates;
    }

    public function addMover(Mower $mover)
    {
        $this->movers[] = $mover;

        if($mover->getPathLength() > $this->longestPathLength)
        {
            $this->longestPathLength = $mover->getPathLength();
        }
    }

    public function getAllMoverPathsInArray()
    {
        $mover_paths = array();
        foreach($this->movers as $mover)
        {
            $mover_paths[$mover->getInitialPosition()->toString()] = $mover->getMovePath();
        }

        return $mover_paths;
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