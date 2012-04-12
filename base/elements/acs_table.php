<?php


/**
* Private class (it's suppose to be private because only the acs_table_tr and acs_table_td should use it) to add some methods to the acs_properties_handler class
* 
*/
/*
class acs_table_data_ extends acs_properties_handler {

    protected $_data = array();

    protected function add_data($data) {
        $this->_data[] = $data;
        return $this;                    
    }
    
    public function getHtml($data = null) {    
        $start = str_replace(
                            '%attributes%',
                            $this->getProps(),
                            $this->_html
                );                
                
        if (!empty($this->_data))             
            $start .= implode('',$this->_data);

        return $start . $this->_html_end;
    }
} */

class acs_table_data extends acs_element {

	protected $_data = array();

	protected function add_data($data) {
		$this->_data[] = $data;
		return $this;                    
	}
/*	
	public function getHtml($data = null) {	
		$start = str_replace(
							'%attributes%',
							$this->getProps(),
							$this->_html
				);                
				
		if (!empty($this->_data)) 			
			$start .= implode('',$this->_data);

		return $start . $this->_html_end;
	}
	*/
}

/**
* acs_table
* 
* Simple class to generate tables
* 
* Table info: 
*   - http://www.w3schools.com/tags/tag_table.asp
*   - http://www.w3.org/TR/CSS2/tables.html
*/

class acs_table extends acs_table_data {
    	
	/***********************/
	/* Private Properties */
	/**********************/
	
	private $_trs = array();
	
	//This will be used to referece all the table rows and all the table data
	//if I want to set something to all the table rows or table data I will use these 
	public $trs;
	public $tds;
    
    
	
	public function __construct($data = null, $props = null) {
		
        //TODO: Change these to methods instead of properties. This way I will only instantiate the classes if they are called
		$this->trs = new acs_table_tr();
		$this->tds = new acs_table_td();	
				
		if ($data) 
			$this->add_row($data,$props);
			
		$this->tpl_path = 'html/table/table';
	}

		
	/**
	 * @deprecated Use td()
	 */
	public function rowref($number) {
		return $this->tr($number);
	}
	
	/**
	 * 
	 * Return refered td
	 * @param int $ref
	 * @return acs_table_tr	 
	 */
	public function tr($ref) {
		if (!empty($this->_trs[$ref]))
			return $this->_trs[$ref];
        
        return null;
	}
	
	/**
    * Return the last row
    * 
    * @return acs_table_tr    
    */
	public function last_row() {
		return $this->rowref((count($this->_trs) - 1));
	}
	
	/**
	* Method do add a Table row object
	* 
	* @param mixed $data - Data to add, can be a two dimensional array
	* @param array $props - A propertie passed in the form array(<propertie name>, <propertie value>)
	* @return acs_table_tr
	*/
	public function add_row($data = null, $props = null) {
        if ($data) {
		    if (!is_array($data)) 
			    $this->_trs[] = new acs_table_tr($data,$props); 								
            else
		        foreach ($data as $table_row)
			        $this->_trs[] = new acs_table_tr($table_row,$props);
        }
        else 
            $this->_trs[] = new acs_table_tr;        
    
		return $this->last_row();	
	}

	/**
    * Create a striped table
    * 
    * @param string $coloreven
    * @param string $colorodd
    */
	public function stripe($coloreven,$colorodd = null) {       
		foreach ($this->_trs as $k => $tr) {
			$colortouse = null;
				
			if (helper_math::isOdd($k)) {
				if ($colorodd) 
					$colortouse = $colorodd;
			}
			else 
				$colortouse = $coloreven;					
			
			$tr->style = 'background-color: ' . $colortouse;
		}
	}
    		
    
    protected function beforeHtml() {
        $elements = array();
        
        foreach ($this->_trs as $trs) 
            $elements[] = $trs->html();
        
        return array('elements' => implode(PHP_EOL,$elements));            
    }
}


/**
* Table row object class
*/
//class acs_table_tr extends acs_table_data {
class acs_table_tr extends acs_element {
	
	private $_tds = null;       

	public function __construct($data = null) {		
		if ($data)
			$this->add_tdata($data);
			
		$this->tpl_path = 'html/table/table_tr';
	}
	
	/**
	* Method to apply the props given to all the table datas of this table row
	* 
	* @param mixed $data - The properties
	*/
	public function attritoalltds(array $props) {
		for ($i = 0,$t = count($this->_tds);$i<$t;$i++) 
			$this->_tds[$i]->mergeAttributes($props);
	}
	
	/**
	* Method to add table data objects with the provided data
	* 
	* @param mixed $data
	* @return mixed
	*/
	public function add_tdata($data = null, $props = null) {
		if (is_array($data)) {
			foreach ($data as $item) {            
                $ref = $this->add_tdata($item);                                
            
				if ($props) {
					list($prop,$value) = $props;
					$ref->$prop = $value;
				}					
			}
		}
		else {            
			$this->_tds[] = new acs_table_td($data);
			return $this->_tds[(count($this->_tds) - 1)];
		}
	}
	
	/**
	 * 
	 * Return the refered td class
	 * @param int $ref
	 * @return acs_table_td 
	 */
	public function td($ref) {
		if (!empty($this->_tds[$ref])) 
			return $this->_tds[$ref];
	}
    
    /**
    * Return reference to last td class in the array of tds
    * 
    */
    public function last_td() {
        return $this->td(count($this->_tds) - 1);
    }
    
    public function beforeHtml() {
        $tds = array();        
        if (!empty($this->_tds))        
            foreach ($this->_tds as $tds_data) 
                $tds[] = $tds_data->html();        
        
        return array('tds' => implode(PHP_EOL,$tds));        
    }		
}

/**
* Table Data object class
*/
class acs_table_td extends acs_element {    
    
    private $_td_data = null;
    
    public function data($data) {
        $this->_td_data = $data;        
    }

	public function __construct($data = null) {   		
		if ($data)
			$this->data($data);
			
		$this->tpl_path = 'html/table/table_td';
	}
    
    protected function beforeHtml() {
        return array('data' => $this->_td_data);
    }
}

