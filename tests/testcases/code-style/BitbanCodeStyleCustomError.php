<?php

// base class with member properties and methods
class Vegetable {

	var $edible;
	var $color;
    private $_privateVar;

	function Vegetable($edible, $color="green") {
		$this->edible = $edible;
		$this->color = $color;
	}

	// This line has a          commment tooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo wide
    
	function is_edible() {
		return $this->edible;
	}

	function what_color() {
		return $this->color;
	}

    private function _iAmPrivate()
    {
        return $this->_privateVar;
    }

} // end of class Vegetable
