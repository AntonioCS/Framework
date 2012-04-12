<?php

/**
* Implementation of the Active Record pattern (still in development)
* 
* It must inherit from acs_db
*/
abstract class acs_activerecord extends acs_db {

    /**
    * This will be used to keep track of filling the class var fields with the values of the selected table fields	
    * 
    * @var mixed
    */
    private $_selected_data = null; 
    /**
    * This will have the values so I am able to change them   
    * The pdo result set is not changable so I must use this 
    * This will basically serve to hold data that will be called in the update command
    * 
    * @var array
    */
    private $_update_data = null; 


    /**
    * This will hold the conditions (if any) for the update
    * 
    * @var string
    */
    private $_update_data_conditions = null;
					       
    /**
    * holds the value of the selected id
    * 
    * @var int
    */
    private $_selected_id = null;

    /**
    * this will hold the id's marked for being deleted. This can either be one or N id's
    * 
    * @var mixed
    */
    private $_delete_ids = array();
    /**
    * This will hold the conditions to use on the delete query
    * 
    * @var mixed
    */
    private $_delete_conditions = array(); 

    /**
    * This will be set to true when the user calls delete with no parameters and no id is selected
    * 
    * @var bool
    */

    private $_truncate_table = false;

    /**
    * This will be used to determin if the save method has been called
    * 
    * @var mixed
    */
    private $_saveCalled = false; 

    //properties that will hold the values to be inserted along with the field where they will be inserted   
    /**
    * This will be used in the insert method
    *  
    * @var mixed
    */
    private $_insert_data = null;
    /**
    * This will be used in the __set override method for the insert stuff
    * 
    * @var mixed
    */
    private $_set_insert_data = null;

    /**
    * this will hold an assosiative array, being the key names the names of the fiels so that I can check if a field is valid by just using an isset on this array
    * 
    * @var array
    */
    private $_easyCheckFields = array();
    
    
    /**
     * 
     * Contains the total number of fields minus the primary key
     * @var int
     */
    private $_totalfields = 0;

    /**
    * holds the name of the current field name
    * Override this if you want another table (or just use the change_table method)
    * 
    * @var string
    */
    protected $_tablename = null;
    /**
    * holds an array containing information about all the fields of the table
    * 
    * @var array
    */
    protected $_fields = array();
    /**
    * comma separated fields names
    * 
    * @var string
    */
    protected $_fieldsNames = null;

    /**
    * name of the primary key
    * 
    * @var string
    */
    protected $_primarykey = null;

    /**
    * Propertie to determine if the schema as been created   
    * 
    * @var bool
    */
    private $_isSchema = false;

    /**
    * Propertie to determine if a connections has been created
    * 
    * @var bool
    */
    private $_connectionCreated = false;


    /**
    * Propertie that will hold the id of the newly inserted row
    * 
    * @var int
    */
    public $id = null;
    
    
    /**
     * Hold database connection 
     * 
     * @var acs_db
     */
    private $_db = null;
    
    /**
     * 
     * @var mixed
     */
    private $_db_settings = null;


    public function __construct() {				    
    	parent::__construct();
	    
	    //let's get the table to wich we are binding
	    if ($this->_tablename == null)
		    $this->_tablename = get_class($this); //__CLASS__ <-- Returns this class and not the subclass (which is what we want)           

		//Setting default connection
		$this->_db_settings = $this->db_settings(); 
		    		    
	    //$this->schema(); - trying to create a way of lazy connecting
    }
    
    
    /**
     * Override this if you want another db setting
     * 
     */
    protected function db_settings() {
   		return $this->configData->dbp_default;
    }
      

    /**
    * With this the save method is called when the class goes off stack and if the auto commit is on 
    * 
    */
    public function __destruct() {
	    if ($this->configData->dbmsd_autocommit)
		    $this->save();
    }    

    /**
     * Create or recreat the working schema for this table
     *  
     * @param boolean $recreate If you want to recreate the schema stuff even if it exists (default's to false)
     * @param boolean $nocache if set to true a cache file of the schema of the table will not be created (defaults to false)
     */
    public function schema($recreate = false,$nocache = false) {    
	    $this->_isSchema = false;
	   // $c = new acs_cache('schema/');
	    $schema_name = $this->_tablename . '_schema';	    	   
	    
	    if (!$this->isTable($this->_tablename))
		    throw new acs_exception(DB_NOTABLE . ': ' . $this->_tablename);
	    
	    $fields = array();
	    
	    //if (!is_file($c->isCacheFile($schema_name)) || $recreate || true) {
	    if (true) {
	        		
		    $names = array();
		    $array_key_name = array();
		    
		    $this->query('Describe ' . $this->_tablename);
		    
		    while (($r = $this->fetch_result()) !== null) { //Using these as class properties (thanks to PDO)			
			    //$names[] = $r->Field; //put all the names here
			    		    	
			    if ($r->Key == 'PRI') //primary key
				    $fields['Primary_key'] = $r->Field;                                        				
			    else {
				    $this->_totalfields++;
				    
				    $fieldarrayname = helper_strings::replletras($r->Field,true);
				    
				    $names[] = $r->Field; //Don't add the PrimaryKey to this
				    $array_key_name[] = $fieldarrayname;

				    //$formatedFieldName = helper_strings::replletras($r->Field,true);					
				    //$fields['Fields'][$formatedFieldName] = array(
				    $fields['Fields'][$fieldarrayname] = array(
											    'Name' => $r->Field,
											    'Type' => $r->Type, 
											    'Null' => ($r->Null == 'YES'),
											    'Default' => $r->Default,
				    							'Extra' => $r->Extra
										    );
			    }
		    }
		    $fields['FieldsNames'] = implode(',',$names);
		    $fields['FieldArrayKeys'] = implode(',',$array_key_name);		    
		    /*
			if (!$nocache) {
				$c->save($schema_name,$fields);			
			}*/
	    }
	    else {
		    //$fields = $c->get($schema_name);
		    $this->_totalfields = count($fields['Fields']);
	    }
	    
	    $this->_fields = $fields;
	    $this->_fieldsNames = $fields['FieldsNames'];     
	    $this->_primarykey = (isset($fields['Primary_key']) ? $fields['Primary_key'] : null); //We now have the name of the primary id    
	    	    
	    $this->create_easyCheckFields();
	    
	    $this->_isSchema = true;        
    } 


    /**
    * Method to update the specified fields
    * 
    * @param array $data Values to set the fields in the format, array('FieldName' => 'NewValue','FieldName2' => 'NewValue2'....)
    * @param string $conditions String with the conditions to use to update the fields
    */
    public function update($data,$conditions = null) {
	    $this->_update_data = $data;
	    
	    $this->_update_data_conditions = null;
	    if ($conditions)
		    $this->_update_data_conditions = $conditions;
	    
	    
	    if ($this->configData->dbmsd_autocommit)
		    $this->save($conditions);
	    else 
		    return $this;		    	
    }

    /**
    * This function is to be called when I have inserted data, updated or deleted data. This is the method that will actually do all those things
    * 
    * @return int
    */
    public function save() {
	    $this->isInitiated();
	    
	    $this->_saveCalled = true;
	    //update
	    if ($this->_update_data) {  //The values will only be saved if there is any change to the data I selected (If I did select something)
		    $query = 'UPDATE ' . $this->_tablename . ' SET ';
		    
		    //Set up the data (field = value, field2 = value etc)
		    $setdata = $this->update_query_string_data();
	    
		    $query .= $setdata;
		     
		    if ($this->_selected_id) //it is possible that we don't have an id
			    $query .= ' WHERE ' . $this->_primarykey . ' = ' . $this->_selected_id;
			    
		    //there is also possible to have a condition where this will be saved
		    if ($this->_update_data_conditions) {
			    if ($this->_selected_id) 
				    $query .= ' AND ';
			    else {
				    $query .= ' WHERE ';
				    //let's see if the update condition is just a number
				    if (is_numeric($this->_update_data_conditions)) //if it is is might just be an id 
					    $query .= $this->_primarykey . ' = ';		                		                							
			    }
			    $query .= $this->_update_data_conditions;
							    
			    $this->_update_data_conditions = null;
		    }
		    
		    //LIMIT 1 condition
		    if ($this->configData->dbmsd_update_limit)
			    $query .= ' LIMIT 1'; 
			    
		    
		    $this->_selected_data = $this->_update_data = null; //clear all the data
		    //echo $query;
		    return $this->query($query)->rowCount(); //this will return the number of rows altered if successful			
	    }
	    //insert
	    if (!empty($this->_insert_data) || !empty($this->_set_insert_data)) {
	    	$this->save_insert();	    	
		    return true;
	    }
	    //delete
	    if (!empty($this->_delete_ids) || !empty($this->_delete_conditions) || $this->_truncate_table) {      
		    if ($this->_truncate_table) {
			    $queryDelete = 'TRUNCATE TABLE ' . $this->_tablename;
		    }
		    else {      
			    $queryDelete = 'DELETE FROM ' . $this->_tablename;
			    if (!empty($this->_delete_ids)) {
				    $queryDelete .= ' WHERE ' . $this->_primarykey;
				    if (count($this->_delete_ids) > 1) 
					    $queryDelete .= ' IN(' . implode(',',$this->_delete_ids) . ')';                
				    else
					    $queryDelete .= ' = ' . current($this->_delete_ids);

				    $this->_delete_ids = array(); 
			    }
																								    
			    ///* 
			    //Must find a way to correctly implement this
			    elseif (!empty($this->_delete_conditions)) {
				    $this->log->debug("Entrei nas condicoes do save", $this->_delete_conditions);
				    $delfields = '';
				    foreach ($this->_delete_conditions as $fieldTodelete) {
					    if ($delfields != '')
						    $delfields .= ' OR ';
					    $delfields .= "($fieldTodelete)";
				    }                
				    $queryDelete .= ' WHERE ' . $delfields;                
			    }
		    }
		    //*/        	
		    //$this->log->debug("Query delete: $queryDelete");
		    $this->query($queryDelete);
	    }
	    return null;
    }
        
    private function save_insert() {    	
    	//If the _set_insert_data array is not empty, it means that we have one row that is not completed
     	if (!empty($this->_set_insert_data)) 
     		$this->createInsertArray();
    	
    	$fieldsarray = explode(',',$this->_fieldsNames);
    	
    	foreach ($this->_insert_data as $data) {
    		$args = array();
    		$fields = array();
    		
    		//To maintain the order of the values
    		foreach ($fieldsarray as $field) {
    			if (isset($data[$field])) {
 					$fields[] = $this->getActualFieldName($field);   			
    				$args[] = $data[$field];
    			}
    		}
    		
    		$q = 'INSERT INTO ' . $this->_tablename . '(' . implode(',',$fields) . ')
    				VALUES(' . implode(',', array_fill(0, count($args),'?')) . ')'; //little one liner from http://www.php.net/manual/en/function.str-repeat.php#90555    	
    		
    		$this->query($q, $args);    		    		
    	}
    	
	   	//Auto populating the id propertie with the last id if there is an id
		if ($this->_primarykey)
			$this->id = $this->lastid();  
			
		$this->_insert_data = null;
    }
    
    /**
    * Wrapper to simplify the truncate process and to make sure the table is truncated.
    * With the delete()->save() method, if an id had been previouslly selected it would delete that id and not truncate the table
    * 
    */
    public function truncate() {		
	    $this->_truncate_table = true;
	    $this->save();	
    }

    /**
    * Wrapper for the $this->conn->lastInsertId() method
    * 
    */
    public function lastid() {
	    return $this->getConn()->lastInsertId();	
    }

    /**
    * Delete method
    * This will mark either the selected field (selected via the select method) or 
    * it will delete what ever id's have been placed or just anything that you have placed as a condition
    * 
    * @return acs_activerecord instance
    */
    public function delete() {
	    if (!func_num_args()) { //no id's
		    if (!$this->_selected_id) {							    
			    //throw new acs_exception('No id to delete'); <-- The user might want to delete all the data
			    $this->_truncate_table = true;		
			    return $this;		
		    }
		    else 
		    	$this->_delete_ids[] = $this->_selected_id;
	    }
	    else { //if we have many id's or other arguments
		    $arguments = func_get_args();
		    $rangePattern = '/^(\d+)-(\d+)$/';
		    $argMatches = null; 
		    
		    foreach ($arguments as $argument) {
			    if (is_numeric($argument)) {
				    $this->_delete_ids[] = $argument;
				    //continue;
			    }
			    elseif (is_array($argument)) {
				    //$this->log->debug("Entrei aqui - é array", $argument);
				    foreach ($argument as $fieldname => $value) {
					    //$this->log->debug("Fieldname: ", $fieldname, " Value: " , $value);
					    $this->_delete_conditions[] = $fieldname . '=' . (is_numeric($value) ? $value : "'$value'");
					    //$this->log->s("_delete_conditions -> ", $this->_delete_conditions);
				    }
			    }
			    elseif (preg_match($rangePattern,$argument,$argMatches)) {
				    list(,$min,$max) = $argMatches;						
				    $this->_delete_ids = array_merge($this->_delete_ids,range($min,$max));
				    //continue;
			    }
		    }
	    }
	    
	    if ($this->configData->dbmsd_autocommit)
		    $this->save();
	    else 
		    return $this;		
    }
	    
    /**
    * This private function will be used to just create the update query string from the data variable
    * if the data var contains this
    *   array("field1" => "value1","field2" => "value2")
    * this will return
    * 
    * "field1" = "value1","field2" = "value2"
    *
    * NOTE: this will not return the value incased with "" if the value is numeric 
    * @return update query string
    * 
    */
    private function update_query_string_data() {
	    if ($this->_update_data) {
		    $setdata = array();
		    foreach ($this->_update_data as $fieldname => $fieldvalue) 
			    $setdata[] = $fieldname . ' = ' . (is_numeric($fieldvalue) ? $fieldvalue : "'$fieldvalue'");               
		    
		    return implode(',',$setdata);
	    }   
	    return null;
    }

    /**
    * Overload of the get function so that the user can access the data selected
    * 
    * @param mixed $fieldname
    * @return mixed
    */
    public function __get($fieldname) {        
	    $p = parent::__get($fieldname);
	    if ($p)
		    return $p;

	    if (isset($this->_selected_data->$fieldname)) //I will return the data in the result set if it doesn't exist in the $data array, ie if it hasn't been changed
		    return isset($this->_update_data[$fieldname]) ? $this->_update_data[$fieldname] : $this->_selected_data->$fieldname;                    
	    return null;        
    }
    /**
    * Overload of the set method
    * 
    * @param mixed $fieldname
    * @param mixed $value
    */
    public function __set($fieldname,$value) {
	    $this->isInitiated();
			    
	    if ($this->field_isValid($fieldname))  {		
		    if ($this->_selected_data) {
			    $this->_saveCalled = false;
			    //Instead of throwing an exception I will create the variables in a special array so that they might be inserted
			    //if (isset($this->_selected_data->$fieldname))  
			    if (property_exists($this->_selected_data,$fieldname))  
				    $this->_update_data[$fieldname] = $value;                    
			    else 
				    throw new acs_exception(ACTIVERECORDING_ERROR_NOFIELD);             
		    }
		    else {//Will be set for insertation                   
			    $this->_saveCalled = false;  
			     /*
				    this has to be an array of arrays because I have to create a table
				    of these values, so for example if I have a table with the fields text,day:
				    _ser_insert_data['text'][] = "bla";
				    _ser_insert_data['day'][] = "monday";
				    
				    With this approach when I am inserting this into the table I can simply do:
				    
				    _ser_insert_data['text'][0],_ser_insert_data['day'][0] 
				    
				    And get the correct values               		
			     */                   				      
			    //$this->_set_insert_data[':' . $fieldname][] = $value;  
			    $this->_set_insert_data[$fieldname][] = $value;  
			    //$this->_set_insert_data[$fieldname][] = $value;  
			    
			    //try to speed things up
			    //if we have the same number of arrays as we have of fields that means we have a row
			    //echo "total fields: " . $this->_totalfields . "<br>";
			    //echo "fields no set: " . count($this->_set_insert_data);
			    ///*
			    if ($this->_totalfields == count($this->_set_insert_data)) {
			    	$this->createInsertArray();
				    /*$data = array();
				    foreach ($this->_set_insert_data as $k => $value) 
					    $data[$k] = $value[0];	                		
				    
				    //if we have a row we just insert it in the insert array and clear this array  
				    //inject directly into the insert array
				    $this->_insert_data[] = $data;
				    $this->_set_insert_data = null;
				    //*/                	
			    }
			    //*/                
		    }
	    }   
	    //else                 
		    //throw new acs_exception(ACTIVERECORDING_ERROR_NOFIELD . ' - ' . $fieldname);        
    }

    /**
     * Create a row in the _insert_data with the fields in the _set_insert_data
     * @TODO URGENT Get this working there is a problem in the insert process. I am inserting all the fields when
     * it's possibles that not all fields are there 
     */
    private function createInsertArray() {
    	$data = array();
	    foreach ($this->_set_insert_data as $k => $value) 
		    $data[$k] = $value[0];
		    	    
	    //if we have a row we just insert it in the insert array and clear this array  
	    //inject directly into the insert array
	    $this->_insert_data[] = $data;
		$this->_set_insert_data = null;   
    	
    }
    
    private function getActualFieldName($field) {
    	return $this->_fields['Fields'][$field]['Name'];
    }
	    
    /**
    * This method will add data to the _insert_data array  
    * 
    * @param array $data associative array of data to insert 
    * @return class instance
    * 
    * @DONE Make this go to the __set method I am inseting in the array data from the outside BIG PROBLEM
    */
    public function insert(array $data) {
    	foreach ($data as $k => $value) 
			$this->__set($k, $value);    		    
	    	    
	    return $this;
    }
																						       
    /**
    * This will fill the private variable selected_data with the data row from the given id
    * This is only to be used when there is a primary key
    * 
    * @param mixed $id 
    */
    public function select($id) {        
	    $this->isInitiated();
	    
	    if (!$this->_primarykey)
		    throw new acs_exception(ACTIVERECORDING_ERROR_NOPRIKEY);
	    
	    if ($id) {            
		    $this->_update_data = null; //reset the data holder
		    $this->_selected_id = $id;
	    		   
		    $query = 'SELECT * FROM ' . $this->_tablename . ' WHERE ' . $this->_primarykey . ' = ?';		    	    		   
		    $this->query($query,$id);
			    		    
		    //instead of an exception just set this to null if there are no results
		    $this->_selected_data = $this->ifresult() ? $this->fetch_result() : null;
		    return $this;          
	    }
	    else 
		    return false;//throw new acs_exception(ACTIVERECORDING_ERROR_NOSELID);
    }
           
    /**
    * This method will be used to select fields from the database (not based on id's but on values used by the user)
    * 
    * @param string $fields
    * @param string $conditions
    * @return acs_activerecord instance
    */
    public function fetch($fields = '*',$conditions = null, $args = null) {
    	$this->isConnected();
    	
	    $this->_selected_id = null;
	    $query = "SELECT $fields FROM " . $this->_tablename;
	    		
	    if ($conditions) {
		    if (stripos($conditions,'where') === false) //if there is no 'where' we will add one
			    $query .= ' WHERE';
			
		    $query .= ' ' . $conditions;
	    }
	    	    
	    $this->query($query, $args);
	    return $this;   
    }

    /**
    * This method will be used to set up a new table and create the schema for that table
    * 	
    * @param mixed $newtable
    */
    public function change_table($newtable) {
	    if ($this->_tablename != $newtable) {
		    $this->_tablename = $newtable;
		    $this->reset_data();
		    $this->schema(true);
	    }
	    return $this;
    }

    /**
    * Overloading the call method so that I can fetch the fields that are directly on the function name
    * 
    * @param mixed $methodname
    * @param mixed $params
    */
    public function __call($methodname,$params) {     

    	//Fetch method magic call implementation 
	    if (strcasecmp(substr($methodname,0,5),'fetch') === 0) {
		    $fields = str_replace('_',',',substr($methodname,6)); //return all but fetch_        	
		    
		    switch (count($params)) {
		    	case 0:
		    		$this->fetch($fields);
		    	break;
		    	case 1:
		    		$this->fetch($fields,$params[0]);
		    	break;
		    	case 2:
		    		$this->fetch($fields,$params[0],$params[1]);
		    	break;
		    }		    
	    }                                          
	    else 
		    throw new acs_exception("Unknown method - $methodname");  
    }

    /**
    * This method will be used to create self made queries
    * When this is used, it will return the PDOStatement object and the user will have to handle it on it's own
    */	
    public function setquery($query, $args = null) {        
	    $this->isInitiated();
	    return $this->query($query, $args);
    }

    /**
    * This will return the total number of rows in the table
    * @return total number of rows
    * 
    */
    public function total($condition = null) {
        $this->isInitiated();
        // Keep count(*) - http://www.mysqlperformanceblog.com/2007/04/10/count-vs-countcol/    
        
	    $query = "SELECT count(*) FROM {$this->_tablename} ";
        if ($condition) {
        	if ($this->hasWhere($condition))
            	$query .= 'WHERE ';
            
            $query .= $condition;
        }
        $res = $this->query($query);
        if ($res)
	        return current($this->query($query)->fetch());		
        
        return null;
    }
    
															       
    /**
    * this method will allow me to reset the update data that was written 
    * 
    */
    public function reset_data() {
	    $this->_update_data = null;   
    }
	       
    /**
     * Return the type and limit of the given field (as it is stated in the array created by the schema function
     * @param string $fieldname
     */
    private function getTypeLimit($fieldname) {
	    $type = $this->_fields['Fields'][$fieldname]['Type'];
	    if (preg_match('/(.+)\((.+?)\) (.+)/',$type,$data)) {
			     array_shift($data);
			     
			     for ($i = count($data);$i < 3;$i++) //This is just so that I will always return an array with 3 items
				    $data[] = null;
			     
			     return $data;
	    }
	    return array(null,null,null);
    }
       
    /**
    * Create an associative array with all the fields. So instead of in_array I can just use isset
    * 
    */
    private function create_easyCheckFields() {
	    $fields = explode(',',(($this->_primarykey) ? $this->_primarykey . ',' : null) . $this->_fieldsNames);
	    foreach ($fields as $field)
		    $this->_easyCheckFields[$field] = 1; //This way I only have to use the isset to check if the $field is valid           
    }
      
    /**
    * Simple method to check if a given field is valid. By valid I mean that it exists in the selected table 
    * 
    * @param string $check_field
    * @return bool
    */
    private function field_isValid($check_field) {       
	    return isset($this->_easyCheckFields[$check_field]);
    }  

    private function fieldType_value($field) {         
	    if ($this->field_isValid($field)) {
		    $type = $this->_fields['Fields'][$field]['Type'];
		    $match = array();
		    if (preg_match('/\w+/',$type,$match))   //with this simple pattern I strip out the (n) part of the type                
			    return $match[0];    				            
	    }        
	    return null;
    }

    private function fieldType($field) {
	    if (!$this->field_isValid($field))             
		    return null;     
		       
	    $type = 'unknown';       
	    switch ($this->fieldType_value($field)) {
		    //http://dev.mysql.com/doc/refman/5.0/en/string-type-overview.html
		    case 'varchar': //A variable-length string. M  represents the maximum column length in characters. 
						    //In MySQL 5.0, the range of M  is 0 to 255 before MySQL 5.0.3, and 0 to 65,535 in MySQL 5.0.3 and later.
		    case 'char': //The range of M is 0 to 255. If M is omitted, the length is 1. 
		    case 'tinytext': //A TEXT column with a maximum length of 255 (2^8 � 1) characters. The effective maximum length is less if the value contains multi-byte characters. Each TINYTEXT value is stored using a one-byte length prefix that indicates the number of bytes in the value. 
		    case 'tinyblob': //A BLOB column with a maximum length of 255 (28 � 1) bytes. Each TINYBLOB value is stored using a one-byte length prefix that indicates the number of bytes in the value. 
		    case 'text': //A TEXT column with a maximum length of 65,535 (2^16 � 1) characters. The effective maximum length is less if the value contains multi-byte characters. Each TEXT value is stored using a two-byte length prefix that indicates the number of bytes in the value.  	
		    case 'blob'://A BLOB column with a maximum length of 65,535 (2^16 � 1) bytes. Each BLOB value is stored using a two-byte length prefix that indicates the number of bytes in the value.  	
		    case 'mediumtext': //A TEXT column with a maximum length of 16,777,215 (2^24 � 1) characters. The effective maximum length is less if the value contains multi-byte characters. Each MEDIUMTEXT value is stored using a three-byte length prefix that indicates the number of bytes in the value. 
		    case 'mediumblob': //A BLOB column with a maximum length of 16,777,215 (2^24 � 1) bytes. Each MEDIUMBLOB  value is stored using a three-byte length prefix that indicates the number of bytes in the value. 
		    case 'longtext': //A TEXT column with a maximum length of 4,294,967,295 or 4GB (232 � 1) characters. The effective maximum length is less if the value contains multi-byte characters. The effective maximum length of LONGTEXT  columns also depends on the configured maximum packet size in the client/server protocol and available memory. Each LONGTEXT  value is stored using a four-byte length prefix that indicates the number of bytes in the value.  	
		    case 'longblob': //A BLOB column with a maximum length of 4,294,967,295 or 4GB (232 � 1) bytes. The effective maximum length of LONGBLOB columns depends on the configured maximum packet size in the client/server protocol and available memory. Each LONGBLOB value is stored using a four-byte length prefix that indicates the number of bytes in the value. 
			    $type = 'string';
		    break;
		    //http://dev.mysql.com/doc/refman/5.0/en/numeric-type-overview.html
		    case 'tinyint': //A very small integer. The signed range is -128 to 127. The unsigned range is 0 to 255. 
		    case 'smallint': //A small integer. The signed range is -32768 to 32767. The unsigned range is 0 to 65535. 
		    case 'mediumint': //A medium-sized integer. The signed range is -8388608 to 8388607. The unsigned range is 0 to 16777215.  
		    case 'int': //A normal-size integer. The signed range is -2147483648 to 2147483647. The unsigned range is 0 to 4294967295.  
		    case 'bigint': //A large integer. The signed range is -9223372036854775808 to 9223372036854775807. The unsigned range is 0 to 18446744073709551615. 
			    $type = 'integer';
		    break; 
		    case 'float': //A small (single-precision) floating-point number. Allowable values are -3.402823466E+38 to -1.175494351E-38, 0, and 1.175494351E-38 to 3.402823466E+38
		    case 'double': //A normal-size (double-precision) floating-point number. Allowable values are -1.7976931348623157E+308 to -2.2250738585072014E-308, 0, and 2.2250738585072014E-308 to 1.7976931348623157E+308
		    case 'decimal':
			    $type = 'float';				
		    break;
		    //http://dev.mysql.com/doc/refman/5.0/en/date-and-time-type-overview.html
		    case 'datetime': //A date and time combination. The supported range is '1000-01-01 00:00:00' to '9999-12-31 23:59:59'. MySQL displays DATETIME values in 'YYYY-MM-DD HH:MM:SS' format, but allows assignment of values to DATETIME columns using either strings or numbers. 
		    case 'date': //A date. The supported range is '1000-01-01' to '9999-12-31'. MySQL displays DATE values in 'YYYY-MM-DD' format, but allows assignment of values to DATE  columns using either strings or numbers. 
		    case 'time': //A time. The range is '-838:59:59' to '838:59:59'. MySQL displays TIME values in 'HH:MM:SS' format, but allows assignment of values to TIME columns using either strings or numbers. 
		    case 'year': //A year in two-digit or four-digit format. The default is four-digit format. In four-digit format, the allowable values are 1901 to 2155, and 0000. In two-digit format, the allowable values are 70 to 69, representing years from 1970 to 2069. MySQL displays YEAR values in YYYY format, but allows you to assign values to YEAR columns using either strings or numbers. 
		    case 'timestamp':
			    $type = 'date';			
		    break;            
	    }
	    //echo $type;
	    return $type;
    }

    private function fieldType_isString($field) {
	    return ($this->fieldType($field) == 'string');
    }
    private function fieldType_isInt($field) {
	    return ($this->fieldType($field) == 'integer');
    }
    private function fieldType_isDate($field) {
	    return ($this->fieldType($field) == 'date');
    }
    public function fieldType_isFloat($field) {
    	return ($this->fieldType($field) == 'float');
    }
    
    public function field_isNull($field) {
    	if ($this->field_isValid($field)) {    		
    		return $this->_fields['Fields'][$field]['Null']; //Boolean value
    	}    	
    	return null;
    }

    public function field_defaultValue($field) {
    	if ($this->field_isValid($field)) {    		
    		return $this->_fields['Fields'][$field]['Default'];
    	}    	
    	return null;
    }

    /**
    * This method will check if I have initiated the connection and if I have a schema for the current table
    * 
    */
    private function isInitiated() {
	    if (!$this->_connectionCreated) {			    			    
	    	
	    	$this->connect($this->_db_settings);
					
		    if (!$this->_isSchema)
			    $this->schema(); 
		    
		    $this->_connectionCreated = true;   	
	    }
    }    

    /**
     * 
     * To check if the string given starts with where
     * @param string $partialquery
     */
    private function hasWhere($partialquery) {
    	return (stripos($partialquery,'where') === false);    	
    }

}