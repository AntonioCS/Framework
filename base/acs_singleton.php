<?php

abstract class acs_singleton {
	
	protected static $_instance = null;
   
    protected function __construct() {}
   
    public static function getInstance() {
    	if (!self::$_instance) {
    		$class = get_called_class();
    		self::$_instance = new $class;    		
    	}     	
    	return self::$_instance;
    }
}