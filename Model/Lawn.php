<?php
/**
 * Created by PhpStorm.
 * User: Wayson
 * Date: 5/22/2015
 * Time: 11:42 AM
 */

class Lawn
{
    private $length;
    private $width;
    private $movers;

    public function __construct($length, $width)
    {
        $this->length = $length;
        $this->width = $width;
        $this->movers = array();
    }

    public function putMover($mover)
    {
        $this->movers[] = $mover;
    }

    /**
     * Run all the mowers
     */
    public function runMovers()
    {

    }

}