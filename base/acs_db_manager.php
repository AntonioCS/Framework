<?php

class acs_db_manager {
	
	private static $_instance = null;
	
	private $_connections = array();
	
	private $_lastcon = array();
	
	private function __construct() {}
	private function __clone() {}
	
	/**	 
	 * Get class instance
	 * 
	 * @return acs_db_manager
	 */
	public static function getInstance() {
		if (!self::$_instance) 
			self::$_instance = new acs_db_manager();
				
		return self::$_instance;	
	}

	/**
	 * Return an existing connection 
	 * 
	 * @param string $type
	 * @param mixed $location Can be a number or a string
	 * 
	 * @return PDO
	 */
	public function getConnection($type, $location = 0) {
		if (isset($this->_connections[$type][$location]))
			return $this->_connections[$type][$location];
		
		return false;
	}

	/**
	 * Create a connection of the specified type with the arguments
	 * 
	 * @param string $type
	 * @param string $name Name for the server given in the acs_config file
	 * @param array $settings
	 * 
	 * @throws PDOException
	 * @throws acs_exception
	 * 
	 * @return PDO
	 */
	public function createConnection($type, $name, array $settings) {
		
		$db = null;
		
		if (($db = $this->getConnection($type,$name)) === false) {		
					
			if (!empty($settings['host']) && !empty($settings['user']) && !empty($settings['pass']) && !empty($settings['database'])) {									
				$host = $settings['host'];
				$user = $settings['user'];
				$pass = $settings['pass'];
				$dbname = $settings['database'];  			   
			}
			else 
				throw new acs_exception("Faulty db parameters");		
	
			//To be able to support various db types I am going to use PDO
			try {		
				//For connection strings
				//http://www.phpro.org/tutorials/Introduction-to-PHP-PDO.html
				switch ($type) {
					case 'mysql':
						$dns = "mysql:host=$host;dbname=$dbname";
						$db = new PDO($dns, $user, $pass);
						//$this->_connections[$type][] = new PDO($dns, $user, $pass);
					break;
					default:
						throw new acs_exception(DBMSD_CONNECTION_TYPE_NOT_SUPPORTED);
				}
			}
			catch (PDOException $e) {//Catch any connection errors
				throw new acs_exception(DBMSD_CONNECTION_ERROR . ': ' . $e->getMessage());
			}					
			
			$this->_connections[$type][$name] = $db;
			
			//Set this as the last connection
			$this->_lastcon[$type] = $name;
		}
		
		return $db;
	}

	/**
	 * Get the last connection resource of the specified type
	 * 
	 * @param string $type
	 */
	public function getLastConnection($type) {
		if (isset($this->_lastcon[$type]) && !empty($this->_connections[$type][$this->_lastcon[$type]])) 
			return $this->_connections[$type][$this->_lastcon[$type]];
		
		return false;
	}
	
	public function __destruct() {
		$this->_connections = null; //Kill all connections
	}
}

