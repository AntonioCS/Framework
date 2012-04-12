<?php


/**
 * Define a custom exception class
 */
class acs_exception extends Exception {
			
    private $_msgshow;
	// Redefine the exception so message isn't optional
	public function __construct($message, $error_code = E_ERROR) {		
    
            if (is_array($message)) { //This will probably happen when it is called via the acs_error_handler function
                $line = $message['line'];
                $file = $message['file'];
                $trace = $message['trace'];
                
                $message = $message['msg']; //must be called last                            
            }
            else {
                $line = $this->line;
                $file = $this->file;            
                $trace = var_export($this->getTrace(),true);
            }    
    
			parent::__construct($message, $error_code);
            
            
            $this->message = $message . '<br />Line: ' . $line . '<br />File: ' . $file;
			
			acs_log::getInstance()->errorlog(
                                                str_replace('<br />',PHP_EOL,$this->message) . PHP_EOL .
                                                'Trace: ' . $trace . PHP_EOL .
                                                '--------------------------------------' . PHP_EOL,
                                                $error_code
                                            );
			
			clearstatcache(); //In case of errors with files (since I use file_exists I should clear the cache when an error occurs just in case there is a file problem)
	}
    

	//custom string representation of object
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}" . PHP_EOL;
	}
}

class acs_exceptionControllerNotFound extends acs_exception {}
class acs_exceptionActionNotFound extends acs_exception {}