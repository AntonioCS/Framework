<?php

/**
* 
*/
class acs_scaffold extends acs_activerecord {
		
	/***********************/
	/* Private Properties */
	/**********************/


	/************************/
	/* Protected Properties */
	/***********************/


	/*********************/
	/* Public Properties */
	/********************/


	/********************/
	/* Private Methods */
	/*******************/


	/**********************/
	/* Protected Methods */
	/*********************/


	/*******************/
	/* Public Methods */
	/******************/

	public function __construct($tablename) {
		$this->_tablename = $tablename; 
		
		parent::__construct();			
		$this->schema(true);
	}

	public function __destruct() {

	}
	
	public function dump() {		
		var_dump($this->_fields);
	}

	/************************/
	/* Overloading Methods */
	/***********************/

	public function __set($propname, $propvalue) {      

	}

	public function __get($propname) {

	}

	public function __isset($propname) {

	}

	public function __unset($propname) {

	}	
}