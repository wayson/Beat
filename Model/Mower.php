<?php
/**
 * Created by PhpStorm.
 * User: Wayson
 * Date: 5/22/2015
 * Time: 11:07 AM
 */

require_once 'Position.php';

class Mower
{
    private $currentPosition;
    private $previousPosition;
    private $initialPosition;

    private $movePath;      //the path the current mower should move
    private $isStop;        //check the current mower has stepped or not
    private $currentStepNumber;   // integer. the char position in $movePath
    private $totalStepCount;    //use this to remember the length of the path in order to avoid keep calling strlen($movePath)

    //private $definedPositions;  // the array the positions the mover traverse

    public function __construct()
    {
        $argv = func_get_args();
        if(is_array($argv[0]))
        {   //if it is an array, then we will say this is about to enter positions and calculate the path
            $this->__constructWithDefinePositions($argv[0]);
        }
        else
        {   //otherwise, this is given the path as string and calculate the final positions
            $this->__constructWithDefinePath($argv[0], $argv[1]);
        }
    }

    private function __constructWithDefinePositions($definedPositions)
    {
        $this->initialPosition = $definedPositions[0];
        $this->previousPosition = $definedPositions[0];
        //$this->definedPositions = $definedPositions;
        $this->movePath = '';

        $this->calculatePath($definedPositions);
    }

    private function __constructWithDefinePath(Position $position, $definedPath)
    {
        $this->currentPosition = $position;
        $this->movePath = $definedPath;
        $this->isStop = false;

        $this->currentStepNumber = 0;
        $this->totalStepCount = strlen($definedPath);

        if($this->totalStepCount == 0)
        {   //if there is no path, then we will treat it as stopped
            $this->isStop = true;
        }
    }

    /**
     * Calculate the path to travers all the positions
     */
    private function calculatePath($definedPositions)
    {
        $definedPositionsLength = count($definedPositions);
        for($i = 1; $i < $definedPositionsLength; $i++)
        {
            if($i == $definedPositionsLength - 1)
            {
                $this->movePath .= 'M';
            }
            else
            {
                $this->movePath .= Position::getPathBetweenTwoConsecutivePositions($this->previousPosition, $definedPositions[$i]);
            }

            $this->previousPosition = $definedPositions[$i];
        }
    }

    public function getPathLength()
    {
        return $this->totalStepCount;
    }


    public function moveToNextStep()
    {
        if($this->isStop == false)
        {
            $this->previousPosition = clone $this->currentPosition;

            //get the next step's action char, then input the position model to change the current position's values
            $this->currentPosition->processAction($this->movePath{$this->currentStepNumber});

            $this->currentStepNumber++; //step to next step

            if($this->currentStepNumber == $this->totalStepCount)
            {   //if it has reached the final step, then we will stop the current mower
                $this->isStop = true;
            }
        }
        else
        {
            $this->previousPosition = $this->currentPosition;
        }
    }

    public function processWholePath()
    {
        for($i = 0; $i < strlen($this->movePath); $i++)
        {
            $this->moveToNextStep();
        }
    }

    public function getCurrentPosition()
    {
        return $this->currentPosition;
    }

    public function getPreviousPosition()
    {
        return $this->previousPosition;
    }

    public function getInitialPosition()
    {
        return $this->initialPosition;
    }

    public function getMovePath()
    {
        return $this->movePath;
    }
}