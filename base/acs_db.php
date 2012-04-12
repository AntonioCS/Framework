<?php 
/**
 * Class to handle database connections
 *  
 * */






class acs_db extends acs_base {

	/**	
	* Will hold the PDO Conneciton object
	* 
	* @var PDO
	*/
	private $conn = null; //propertie that will hold the connection
	/**
	* Row numbers
	* 
	* @var int
	*/
	private $_rowcount = null; 

	/**
	* This will hold the last query 
	* 
	* @var string
	*/
	public $lastquery = null;
	
	private $_lastargs = null;
	
	/**
	* This will hold the result of the last query
	* 
	* @var PDOStatement
	*/
	public $lastresult = null;
	
	/**
	* This will hold the last row returned by fetch_result
	* 
	* @var mixed
	*/
	public $lastreturn = null;
	


	/********************/
	/* Private Methods */
	/*******************/
	
	
	/**
	* These functions will provide an internal counter for the ifresult
	* 
	*/
	private function internalCounter_set() {
		if ($this->lastresult) 
			$this->_rowcount = $this->numrows();
	}
	private function interalCounter_get() {
		if ($this->_rowcount > 0)// !== null)          
			return $this->_rowcount;                    
	}
	private function internalCounter_decrease() {
		if ($this->_rowcount >= 0)
			return --$this->_rowcount;            
	}

	/**
	* small method to check if the query used was a selecte statement
	* 
	*/    
	private function usedSelect() {
		return (substr(strtolower($this->lastquery), 0, 6) == 'select');
	}
	
	/***********************/
	/* Private Methods End */
	/**********************/    
	
	/**********************/
	/* Protected Methods */
	/*********************/
		
	/**
	* Simple function to test connection and to provide lazy connecting :)
	* 
	*/
	public function isConnected() {    	
		if (!$this->conn) 
			$this->connect();   	
	}
	/**
	* Internal Query method  
	* With this method I can keep track of the queries
	* 
	* @param string $query The query to be executed
	* @return PDOStatement
	*/	
	public function query($query, $args = null) {
		if ($this->conn) {
			$this->lastresult = null; //clear the last result
			
			$this->lastquery = $query;
			
			if ($args && !is_array($args))
				$args = array($args);
				
			if (!empty($args)) { //if we have query arguments	
				$this->_lastargs = $args;						
				$q = $this->conn->prepare($query);
							
				if ($q->execute($args))  //returns true on success not the value			
					$this->lastresult = $q;
				else {
					//@todo error code handler
				} 	
			}
			else {		
				$this->_lastargs = null;		            
				$this->lastresult = $this->conn->query($query);

				if ($this->lastresult === FALSE) {
					//@todo error code handler				
				}
			}						
			
			if ($this->lastresult) {
				$this->internalCounter_set();
				return $this->lastresult;	
			}		
		}
		else 
			die('Query attemped without connection');
	}
	
	/**
	 * Method that will check if a given table exists 
	 *
	 * @param string $tableName
	 * @return bool
	 * 
	 * @deprecated Use the isTable method
	 */
	public function CheckDbif_existsTable($tableName) {
		return $this->isTable($tableName);
	}
	
	/*************************/
	/* Protected Methods End */
	/************************/	
	
	/*******************/
	/* Public Methods */
	/******************/
	
	/**
	* Method that will check if a given table exists 
	* 
	* @param string $tablename
	* @return bool
	*/
	public function isTable($tablename) {
		$this->isConnected();		
		return (bool)$this->conn->query("SHOW TABLES LIKE '$tablename'")->rowCount();
	}
		
	public function __construct() {
		parent::__construct();		
	}
	
	//public function connect($host = '', $user = '',$pass = '',$database = '') {
	
	/**
	* This method will try to estabilish a connection to the database
	* Parameters
	* @param mixed $conn parameter with either the name of the connection (set in the config file)
	*                   or an array with this structure:
	*                                            array(
	*                                                   'host' => 'host',
														'user' => 'username',
														'pass' => 'passaword',
														'database' => 'dbname',
														'type' => 'dbtype'
														)
	* 
	*/
	public function connect($conn = null) {
		if ($this->conn) 
			$this->conn = null; //kill existing connection
					
		$conn = $this->getDbSettings($conn);
					
		if ($conn) 	
			$this->conn = acs_db_manager::getInstance()->createConnection($conn['type'],$conn['name'],$conn['settings']);		
		else
			throw new acs_exception('No connection data');
	}	
	
	/**
	 * Return the connection data either from the config data or just return the values given if they follow to the format
	 * 
	 * @param mixed $data	 
	 */
	private function getDbSettings($data = null) {
		//user is passing on the fly data settings
		if (is_array($data)) {
			if (isset($data['type']) && isset($data['name']) && isset($data['settings'])) 
				return $data; 			
		}
		else {
			if (isset($this->configData->dbp[$data])) {
				$conndata['type'] = $this->configData->dbp[$data]['type'];
				$conndata['name'] = $this->configData->dbp[$data]['server'];
				$conndata['settings'] = $this->configData->dbs[$conndata['type']][$conndata['name']];
				
				return $conndata;
			}
			else {
				if ($this->configData->dbp_default && isset($this->configData->dbp[$this->configData->dbp_default])) {
					return $this->getDbSettings($this->configData->dbp_default);
				}
			}
		}
		
		return null;
	}
	

	/**
	*   Wrapper to be used in if statments to just check if the query returned something or not
	* 
	*/
	public function ifresult() {
		return (bool)$this->interalCounter_get();
	}

	/**
	* This will be used to return the row as either:
	*  an array based index 
	*  class like based
	*   row based
	*  @param string $returntype parameter to specify the desired return type. 
	* 
	*  Defaults to obj which does not exist so it goes for the default which is object
	* 
	*/
	public function fetch_result($returntype = 'obj') {
		if ($this->ifresult()) {            
			switch ($returntype) {
				case 'assoc':
					$rtype = PDO::FETCH_ASSOC;
				break;
				case 'row':
					$rtype = PDO::FETCH_NUM;
				break;
				default:
					$rtype = PDO::FETCH_OBJ; 
			}				
			$this->internalCounter_decrease();
			$this->lastreturn = $this->lastresult->fetch($rtype);			
			return $this->lastreturn;  
		}   
		$this->lastreturn = null;
		return null;
	}
	
	/**
	* Return all the data in an array
	* 
	* @param mixed $returntype
	* @return mixed
	*/
	public function fetch_result_all($returntype = 'assoc') {
		$results = array();
		
		while (($data = $this->fetch_result($returntype)) !== null)
			$results[] = $data;
		
		
		return $results;	
	}	
	
	/**
	* Wrappers for the fetch_result function 
	* 
	*/
	public function fetch_result_assoc() {
		return $this->fetch_result('assoc');
	}
	public function fetch_result_row() {
		return $this->fetch_result('row');   
	}
	
	
	/**
	 * Method that will return the number of rows from the last resultset
	 *
	 * @return unknown
	 */
	public function numrows() {		
		if ($this->lastresult) { 
			if ($this->usedSelect()) { //PDO might not return the correct number of rows if a select was used                                
            
                //http://www.php.net/manual/en/pdostatement.rowcount.php                                
                if (($rowcount = $this->lastresult->rowCount()) !== null)
                    return $rowcount;
                
				$lastquery = $this->lastquery;
				$count_sql = 'select count(*) as total from'; //Manual suggests to use count(*) with the same parameters     
				
				$pos = strpos(strtolower($lastquery), 'from') + 4; //this will return the position of the keyword form plus 4 in the last query
				$code = substr($lastquery,$pos,strlen($lastquery)); //This will retrieve the query after the from
									
				$query = $count_sql . $code;      

                // TODO: Fix problems with queries with LIMIT
                // example: 
                //      select count(*) as total from vinhos WHERE v_tipo = 'branco' LIMIT 0, 15 <-- This will return something
                //      select count(*) as total from vinhos WHERE v_tipo = 'branco' LIMIT 15, 15 <-- This will return nothing, not 0, but nothing!!
                
                $r = null;
                if ($r = $this->conn->query($query)->fetch())
                    $r = current($r);
                
				$this->_rowcount = $r;
				return $this->_rowcount;                
			}            
			return $this->lastresult->rowCount(); //PDO way 
		}
		return null;//return $this->lastresult->num_rows;    
	}
    	
	public function errormsg() {
		return $this->conn->errorInfo();	
	}
	
	public function errorcode() {
		return $this->conn->errorCode();
	}
	
	public function formated_error() {
		list($sqlerrorcode,$drivererrorcode,$drivererrormsg) = $this->errormsg();
		return 'SQL Error: ' . $sqlerrorcode . ' - Driver error code: ' . $drivererrorcode . ' - Driver Error msg: ' . $drivererrormsg;	
	}
	
	
	public function getConn() {
		return $this->conn;			
	}
	
	public function __toString() {		
		return $this->lastquery;
	}
	
	/**********************/
	/* Public Methods End */
	/**********************/
	
	
}
