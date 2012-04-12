<?php 
/**
 * Session handling class
 * 
 * This will help me get rid of the $_SESSION array and will automatically set the session_start once it gets instaciated 
 * This has to be a singleton 
 * 
 * */
class acs_session  { //extends acs_singleton {
	
	private static $_instance = null;
	
	private $_sessionName = null;
		
	///*
	public static function getInstance() {
		if (!self::$_instance)
			self::$_instance = new acs_session();		
		return self::$_instance;		
	}
	//*/
	
	protected function __construct() {
		if (!session_id()) //if for some reason the session has been started
			session_start();
				
		$this->_sessionName = $this->getSessionName();
			
		if ($this->get('initiated_session')) {//Prevent the hijack of a session
			session_regenerate_id();
			$this->set('initiated_session',true);
		}	
	}
	
	public function __destruct() {
		session_write_close();		
	}
	
	/**
	 * Destroy the entire session
	 *
	 */
	public function destroy() {
		// Unset all of the session variables.
		$_SESSION = array();

		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (isset($_COOKIE[session_name()])) 
			setcookie(session_name(), '', time()-42000, '/');
					
		// Finally, destroy the session.
		session_destroy();   
	}
	
	private function getSessionName() {
		return str_replace(' ','',acs_config::getInstance()->fw_name . acs_config::getInstance()->version);
	}
	
	/**
	 * Create a unique token id using the name given
	 * 
	 * @param string $name To identify the token
	 */
	public function createToken($name) {
		$uni = uniqid(sha1($name . microtime()), true);
		$session_varname = $this->getTokenName($name); 
		$this->set($session_varname, $uni);
		return $uni;    	
	}
	
	/**
	 * Fetch the token created by createToken
	 * 
	 * @param string $name
	 */
	public function getToken($name) {
		return $this->get($this->getTokenName($name));				
	}
	
	/**
	 * Delete token
	 * 
	 * @param string $name
	 */
	public function deleteToken($name) {
		$this->remove($this->getTokenName($name));
	}
	
	/**
	 * Return the token and delete it
	 * 
	 * @param string $name
	 */
	public function getTokenAndDelete($name) {
		$t = $this->getToken($name);		
		if ($t) {
			$this->deleteToken($name);
			return $t;
		}
		return null;
	}
	
	/**
	 * Return the correct unique token name
	 *
	 * @param string $name
	 */
	private function getTokenName($name) {
		return 'uniqueID_' . $name;
	}
	
	/**
	* This method will create an extra session variable with the name of the variable given adding 
	* _timilimt plus the session name
	* 
	* This variable will then be checked when the variable is called for
	* 
	* @param mixed $varname - Session variable name to set the time limit
	* @param mixed $timelimit - Time limit in seconds for the variable to live
	*/
	public function unsetIn($varname,$timelimit = 60) {
		if ($this->get($varname)) {
			$this->set($this->timeLimitVarname($varname),array($timelimit,time()));		
		}    
	}
	
	/**
	* This method will reset the counter on the variable
	* 
	* @param mixed $varname
	*/
	public function reset_timer($varname) {
		if ($this->isTimeLimited($varname))
			$this->unsetIn($varname,$this->getTimeLimit($varname));		
	}
	
	// --- Time limit helper functions ---
	private function timeLimitVarname($varname) {
		return $varname . '_timelimit_' . $this->getSessionName();
	}
	private function isTimeLimited($varname) {
		return (isset($_SESSION[$this->_sessionName][$this->timeLimitVarname($varname)]));
	}
	
	private function getTimeLimit($varname) {
		return $_SESSION[$this->_sessionName][$this->timeLimitVarname($varname)][0];
	}
	
	/**
	* This method assumes it's a time limited session variable and returns true if it's time to unset
	* 
	* @param mixed $varname
	*/
	private function isItTimeToDie($varname) {
		list($timelimit,$timeset) = $_SESSION[$this->_sessionName][$this->timeLimitVarname($varname)];		
		return ((time() - $timeset) >= $timelimit);
	}
	
	// --- Time limit helper functions END ---
	
	public function set($session_varname,$session_value) {
		$_SESSION[$this->_sessionName][$session_varname] = $session_value;
		return $this;
	}
	
    /**
    * Return a value
    * 
    * @param string $session_varname
    * @return mixed
    */
	public function get($session_varname) {
		if (isset($_SESSION[$this->_sessionName][$session_varname])) {
			//Timelimit
			if ($this->isTimeLimited($session_varname) && $this->isItTimeToDie($session_varname)) {
				$this->remove($session_varname);
				$this->remove($this->timeLimitVarname($session_varname));
				
				return null;
			}
			return $_SESSION[$this->_sessionName][$session_varname];
		}
		return null;	
	}
	
	/**
	 * Unset specified session variable
	 * 
	 * @param string $session_varname
	 */
	public function remove($session_varname) {
		if (isset($_SESSION[$this->_sessionName][$session_varname])) 
			unset($_SESSION[$this->_sessionName][$session_varname]);        
	}
	
	
	public function __set($session_varname,$session_value) {	
		return $this->set($session_varname,$session_value);	
	}
	
	public function __get($session_varname) {
		return $this->get($session_varname);
	}
	public function __unset($session_varname) {
		$this->remove($session_varname);
	}
	
	public function sessionname() {
		return $this->_sessionName;
	}
}
