<?php

namespace Bitban;

// base class with member properties and methods
class BitbanCodeStyleOk
{
    private $edible;
    protected $color;
    private $_privateVar;

    public function __construct($edible, $color = "green")
    {
        $this->edible = $edible;
        $this->color = $color;
    }

    public function isEdible()
    {
        if ($this>$this->_iAmPrivate()) {
            $this->edible = true;
        }
        return $this->edible;
    }

    public function whatColor()
    {
        return $this->color;
    }

    private function _iAmPrivate()
    {
        return $this->_privateVar;
    }
} // end of class Vegetable
