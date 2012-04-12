<?php

/**
* 
*/
class acs_model extends acs_activerecord {
        
    /***********************/
    /* Private Properties */
    /**********************/


    /************************/
    /* Protected Properties */
    /***********************/        
    
    protected $_session = null;


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

    public function __construct() {
    	parent::__construct();    	    
    }
    
    public function init() {}

	/**
	* Get session instance
	* 
	* return acs_model
	*/
	public function getSession() {
		$this->_session = acs_session::getInstance();	
		return $this;
	}    
}