<?php
/**
 * Created by PhpStorm.
 * User: Wayson
 * Date: 5/22/2015
 * Time: 11:12 AM
 */

define('N', 1);
define('E', 2);
define('S', 3);
define('W', 4);

class Position{

    private $cordX;
    private $cordY;
    private $orientation;

    public function __construct($cordX, $cordY, $orientation)
    {
        $this->cordX = $cordX;
        $this->cordY = $cordY;

        switch($orientation)
        {
            case 'N':
                $this->orientation = N;
                break;
            case 'E':
                $this->orientation = E;
                break;
            case 'S':
                $this->orientation = S;
                break;
            case 'W':
                $this->orientation = W;
                break;
            default:
                break;
        }
    }

    public function getCordX()
    {
        return $this->cordX;
    }

    public function getCordY()
    {
        return $this->cordY;
    }

    public static function isSamePosition(Position $positionA, Position $positionB)
    {
        if($positionA->cordX == $positionB->cordX && $positionA->cordY == $positionB->cordY)
        {   //if both of the coordinates value are then same, then we can tell they are in the same position
            return true;
        }
        else
        {
            return false;
        }
    }

    public function processAction($char)
    {
        switch($char)
        {
            case 'L':
                $this->turnLeft();
                break;
            case 'R':
                $this->turnRight();
                break;
            case 'M':
                $this->moveForward();
                break;
            default:
                break;
        }
    }

    private function turnLeft()
    {
        $this->orientation = ($this->orientation - 1) < N ? W : $this->orientation - 1;
    }

    private function turnRight()
    {
        $this->orientation = ($this->orientation + 1) > W ? N : $this->orientation + 1;
    }

    private function moveForward()
    {
        switch($this->orientation)
        {
            case N:
                $this->cordY++;
                break;
            case E:
                $this->cordX++;
                break;
            case S:
                $this->cordY--;
                break;
            case W:
                $this->cordX--;
                break;
            default:
                break;
        }
    }

    public function toString()
    {
        $orient = '';
        switch($this->orientation)
        {
            case N:
                $orient = 'N';
                break;
            case E:
                $orient = 'E';
                break;
            case S:
                $orient = 'S';
                break;
            case W:
                $orient = 'W';
                break;
            default:
                break;
        }

        return $this->cordX . ' ' . $this->cordY . ' ' . $orient;
    }
}