<?php

$app_loaded=true;

class App {
	public initialized = false;

	public __construct(){
		$this->initialized = true;
	}

	public __toString(){
		return ($this->initialized) ? "initialized" : "not initialized" ;
	}
}
