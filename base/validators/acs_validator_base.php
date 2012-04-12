<?php

class acs_validator_base {

    /**
    * Msg on sucess
    * 
    * @var string
    */
    protected $_success_msg = null;
    
    /**
    * Msg on error
    * 
    * @var string
    */
    protected $_error_msg = null;

    /**
    * Set Error Msg
    * 
    * @param mixed $msg
    */
    public function setErrorMsg($msg) {
        $this->_error_msg = $msg;
        return $this;
    }
    
    /**
    * Return Error msg
    * 
    */
    public function getErrorMsg() {
        return $this->_error_msg;
    }
    
    public function setSuccessMsg($msg) {
        $this->_success_msg = $msg;
        return $this;
    }
    
    public function getSuccessMsg() {
        return $this->_success_msg;
    }
    
    public function __construct($succes_msg = null, $error_msg = null) {            
    
        if ($succes_msg)
            $this->setSuccessMsg($succes_msg);        
        
        if ($error_msg)
            $this->setErrorMsg($error_msg);        
    }
    
    public function validate($value) {
        return true;
    }
}