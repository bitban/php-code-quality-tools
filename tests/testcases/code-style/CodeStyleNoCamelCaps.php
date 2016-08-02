<?php

namespace Bitban;

// base class with member properties and methods
class BitbanCodeStyleOk
{
    private $edible;
    protected $no_camel_caps_variable;
    private $_privateVar;

    public function __construct($edible, $color = "green")
    {
        $this->edible = $edible;
        $this->color = $color;
        $no_camel_caps_variable = true;
    }

    public function no_camel_caps_method()
    {
        if ($this>$this->_iAmPrivate()) {
            $this->edible = true;
        }
        return $this->edible;
    }

    private function _no_camel_caps_private_method()
    {
        return $this->color;
    }

    private function _iAmPrivate()
    {
        return $this->_privateVar;
    }
} // end of class Vegetable
