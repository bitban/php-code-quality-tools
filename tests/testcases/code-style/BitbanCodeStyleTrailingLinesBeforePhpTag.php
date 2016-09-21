

<?php

namespace Bitban;

// base class with member properties and methods
class BitbanCodeStyleTrailingLinesBeforePhpTag
{
    private $edible;
    protected $color;

    public function __construct($edible, $color = "green")
    {
        $this->edible = $edible;
        $this->color = $color;
    }

    public function whatColor()
    {
        return $this->color;
    }
}
