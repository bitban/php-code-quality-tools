<?php

// base class with member properties and methods
class Vegetable {

	var $edible;
	var $color;

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

} // end of class Vegetable
