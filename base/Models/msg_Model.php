<?php

class msg extends acs_model {
	
	/**
	* Session holder
	* 
	* @var acs_session
	*/
	private $_s = null;
	
	/**
	* Key name of the array containing all the msg's
	* 
	* @var string
	*/
	private $_k = 'acs_msg';
	
	public function init() {
		$this->_s = acs_session::getInstance()->sessionname();	
	}
	
	/**
	* Create new msg
	* 
	* @param string $key
	* @param mixed $data
	*/
	public function setmsg($key,$data) {
		$_SESSION[$this->_s][$this->_k][$key] = serialize($data);	
	}
	
	/**
	* Retrieve previously stored msg
	* 
	* @param mixed $key
	* @return mixed
	*/
	public function getmsg($key) {
		if (isset($_SESSION[$this->_s][$this->_k][$key]))
			return unserialize($_SESSION[$this->_s][$this->_k][$key]);
		return null;
	}
	
	/**
	* Retrieve previously stored msg and clear the msg
	* 
	* @param mixed $key
	* @return mixed
	*/
	public function getmsg_clear($key) {
		$data = $this->getmsg($key);
		
		if ($data)
			unset($_SESSION[$this->_s][$this->_k][$key]);	
			
		return $data;
	}	

}