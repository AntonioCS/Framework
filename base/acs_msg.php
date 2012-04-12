<?php

/**
* Pass msg's from controller to controller (or any other component) using sessions
* This will automatically serialize all the data
* 
*/
class acs_msg {
		
	/**
	* Session holder
	* 
	* @var acs_session
	*/
	private $_session = null;	
	
    /**
    * Key in the acs_session class
    * 
    * @var string
    */
	private $_k = 'acs_msg';
    
    /**
    * Container for the msg's
    * 
    * @var stdClass
    */
    private $_data = null;
    
    /**
    * Hold the class instance
    * 
    * @var acs_msg
    */
    private static $_instance = null;

	/*******************/
	/* Public Methods */
	/******************/
    /**
    * Get the session key and see if the msg object exists
    * 
    */
	private function __construct() {
		$this->_session = acs_session::getInstance();
        $d = $this->_session->get($this->_k);
        
        if (!$d)
            $this->_data = new stdClass();        
        else 
            $this->_data = unserialize($d);        
	}
    
    
    public static function getInstance() {
        if (!self::$_instance)
            self::$_instance = new acs_msg();
        
        return self::$_instance;    
    }
    
	
    /**
    * Set msg to key
    * 
    * @param string $key
    * @param mixed $data
    */
	public function setmsg($key,$data) {
        $this->_data->$key = serialize($data);
        $this->saveToSession();
        return $this;        
	}
	
    /**
    * Get msg from key
    * 
    * @param string $key
    * @return mixed
    */
	public function getmsg($key) {
        if (isset($this->_data->$key))
            return unserialize($this->_data->$key);
            
		return null;
	}
	
    /**
    * Get msg from key and remove (if exists)
    * 
    * @param string $key
    */
	public function getmsg_clear($key) {
		$data = $this->getmsg($key);
		
		if ($data) {
            unset($this->_data->$key);    
            $this->saveToSession();
        }
			
		return $data;
	}	
    
    /**
    * Remove all msg's
    * 
    */
    public function clearAll() {
        $this->_data = new stdClass();
        $this->_session->remove($this->_k);
    }
    
    /**
    * Save the _data property to the session
    * 
    */
    private function saveToSession() {
        $this->_session->set($this->_k,serialize($this->_data));    
    }
}