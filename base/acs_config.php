<?php 
/*
* This class will hold all the configurations
* 
* This is a singleton class
* 
*/
class acs_config { 

    /**
    * Array that holds all the data
    * 
    * @var array
    */
    private $_data = array();
    /**
    * Instance of the stdClass holding all the data
    * 
    * @var stdClass
    */
    private static $_configInstance = null;
    
    private $_config_cache_name = 'acs_config_cache';        

    private function __construct() {
        $this->_data['pathtodata'] = CONFIGPATH . 'acs_config_data.' . COMMON_EXTENSION;        
        //Path to the cache of the config file. This has to be the config dir because that's the only directory I know at this moment
        $this->_data['cache_config_file'] = CONFIGPATH . $this->_config_cache_name;
        
        if ($this->loadConfig() == false) {        
            $this->setUpConfig();
            self::$_configInstance = (object)$this->_data;
		}
        else { 
        	//DONE: Add way to reset only the url paths (if it's set that way in the options)  
        	if (isset($this->_data['reset_uri']) && $this->_data['reset_uri']) { 
                $this->setUriData(self::$_instance);
        		//$this->setUriData();
			}
        }
    }

    /**
    * Save the _data array as a stdClass, serialized in a file
    */    
    private function saveConfig() {
    	self::$_configInstance = (object)$this->_data;    
        file_put_contents($this->_data['cache_config_file'],serialize(self::$_configInstance));   
    }
                     
    /**
    * Load the config from the 'compiled' file
    * 
    */
    private function loadConfig() {
        if (file_exists($this->_data['cache_config_file'])) {
            $data = unserialize(file_get_contents($this->_data['cache_config_file']));
            
            self::$_configInstance = (object)$data; //instance of stdClass with all the properties public
            //$this->_data = (array)$data;            
            return true;
        }        
        return false;
    }
        
    /**
    * To create the uri data
    * This will set up the correct paths and by separating this into a method I can reset this if needed
    * 
    * @param mixed $data Array containing the data. If this is an object it will be converted to an array and then back again to an object
    */
    //private function setUriData(&$data) {   
    private function setUriData(&$data) {
    	$toobject = false;
    	if (is_object($data)) {
    		$data = (array)$data;
    		$toobject = true;
		}
		//to prevent the _dir_url properties to be re set with the values that had already been altered
		if (empty($data['css_dir_url_o'])) { //o for original
			$data['css_dir_url_o'] = $data['css_dir_url'];
	        $data['js_dir_url_o'] = $data['js_dir_url'];
	        $data['images_dir_url_o'] = $data['images_dir_url'];
	        $data['flash_dir_url_o'] = $data['flash_dir_url'];		
		}
		
		$data['protocol'] = strtolower(current(explode('/',$_SERVER['SERVER_PROTOCOL'])));
        $data['host'] = $data['protocol'] . '://' . $_SERVER['HTTP_HOST']; //In case it's HTTPS
				
		//$t = $data['indexfile'] . '.' . $data['common_extension'];
        $t_ = dirname($_SERVER['PHP_SELF']);
        if ($t_[strlen($t_) -1] != '/')
        	$t_ .= '/';
        
		    		
    	if ($data['using_modrewrite']) {
            $data['correct_uri'] = $data['host'] . $t_;
            $data['uri'] = $data['host'] . $t_;
		}
        else {
            $data['uri'] = $data['host'] . $_SERVER['PHP_SELF'];
            $data['correct_uri'] = $data['host'] . $t_; //This has to be set like this so that the web files get the correct path
        }
        
        //setting up web dirs     - Ja sei qual é o problema... isto quando é chamado de novo ja tem valaores nas directorias
        //tendo os valores atrofia tudo
        
       	$data['css_dir_url'] = $data['uri'] . $data['css_dir_url_o'];
        $data['js_dir_url'] = $data['uri'] . $data['js_dir_url_o'];
        $data['images_dir_url'] = $data['uri'] . $data['images_dir_url_o'];
        $data['flash_dir_url'] = $data['uri'] . $data['flash_dir_url_o'];
    		    	
    	if ($toobject)
    		$data = (object)$data;    	
	}
    
    /**
    * Create the config data using the array data in the acs_config_data
    * 
    */
    private function setUpConfig() {

        //Get the file where the configs are and process the arrays
        require($this->_data['pathtodata']);

        $this->_data = array_merge($this->_data,$acs_confdata);

        if (empty($this->_data['indexfile']))
            $this->_data['indexfile'] = 'index';

        if (empty($this->_data['common_extension']))
            $this->_data['common_extension'] = COMMON_EXTENSION;
        
        $this->setUriData($this->_data);        
       
       //Create the cache file of the config
        if (isset($this->_data['cache_config']) && $this->_data['cache_config'])
            $this->saveConfig();
        else
            if (file_exists($this->_data['cache_config_file']))
                unlink($this->_data['cache_config_file']);            
    }
    
    /**
    * This no longer returns an instance of this class.
    * It returns a stdClass which is the _data array converted to an object
    *
    * @return stdClass instance
    * @deprecated
    */
    public static function getInstance() {
        return self::getConfig();
    }
    
    /**
    * Return the instance of the stClass that holds the config data
    * 
    * @return stdClass    
    */
    public static function getConfig() {
        if (!self::$_configInstance)
            new acs_config();
                          
        return self::$_configInstance;
    }    
}