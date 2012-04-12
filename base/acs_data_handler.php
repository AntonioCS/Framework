<?php

/**
* Create a data handler for the data given in the construct.
* Ex.: Pass the $_POST array to have all the elements in the $_POST array be an instance of the acs_validator class
* 
*/
class acs_data_handler {
  
    /**
    * Holder of the datasource for the validator
    * 
    * @var array
    */
    protected $datasource = null;
  
    /**
    * Hold the validators (one for each value called via the __get magic method)
    * 
    * @var acs_validator
    */
    protected $validators = array();

    public function __construct() {      
        //$this->validator = new acs_validator();
    } 

    /**
    * Set the source of the data. Will possibly be either $_POST or $_GET
    * 
    * @param array $datasource
    */
    protected function setDataSource(array &$datasource) {
        if (!empty($datasource))
            $this->datasource = &$datasource; //also needs the & here
    }
    
    /**
    * Method to create a date from 3 fields
    * 
    * @param string $fieldyear
    * @param string $fieldmonth
    * @param string $fieldday
    * @param string $datefieldname - Name of the field that will hold the date value
    */
    public function toDate($fieldyear,$fieldmonth,$fieldday,$datefieldname) {
        $this->__set(
                      $datefieldname,
                      date($this->validator->date_format,strtotime($this->datasource[$fieldyear] . '-' . $this->datasource[$fieldmonth] . '-'. $this->datasource[$fieldday]))
                    );
    }
    
    /**
    * Same as toDate but for time data
    * 
    * @param string $fieldhour
    * @param string $fieldminute
    * @param string $fieldsecond
    * @param string $timefieldname
    */
    public function toTime($fieldhour,$fieldminute,$fieldsecond = null,$timefieldname) {      
      $this->__set(
                    $timefieldname,
                    date(
                      $this->validator->time_format,
                      strtotime($this->datasource[$fieldhour] . 
                        ':' . $this->datasource[$fieldminute] . 
                          ':'. ($fieldsecond ? $this->datasource[$fieldsecond] : '00')))
                  );      
    } 

    /**
    * Return instance of validator class with the proper value set
    * 
    * @param string $varname
    * @return validator
    */
    public function __get($varname) {
        $value = null;
        if (isset($this->datasource[$varname])) 
            $value = $this->datasource[$varname];
        else
            return null;
            
        if (!isset($this->validators[$varname]))
            $this->validators[$varname] = new acs_validator($value);
                        
        return $this->validators[$varname];
    }

    /**
    * Change the values in the datasource
    * Note: This will only work if the $varname exists in the datasource
    * 
    * @param mixed $varname
    * @param mixed $value
    */
    public function __set($varname,$value) {
        if ($this->datasource && isset($this->datasource[$varname])) {
            $this->datasource[$varname] = $value;        
            
            //Change the value in the validator if one has been created
            if (isset($this->validators[$varname])) {
                $this->validators[$varname]->value = $value;        
            }
        }
    }   
}