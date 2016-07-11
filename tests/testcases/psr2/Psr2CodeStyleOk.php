<?php

namespace Bitban;

// base class with member properties and methods
class Psr2CodeStyleOk
{
    private $edible;
    protected $color;

    public function __construct($edible, $color = "green")
    {
        $this->edible = $edible;
        $this->color = $color;
    }

    public function isEdible()
    {
        return $this->edible;
    }

    public function whatColor()
    {
        return $this->color;
    }
} // end of class Vegetable
