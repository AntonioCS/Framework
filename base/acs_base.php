<?php 

/**
 * 
 * Base class
 * 
 * @author antoniocs
 *
 */
abstract class acs_base {    
    
    protected $configData = null;
    
    /**
     * 
     * @var acs_log
     */
    protected $log = null;
    
    /**
     * 
     * @var acs_cache
     */
    protected $cache = null;
    
    /**
     * 
     * @var array
     */
    protected $_models = array(); //model array  
    
    
    /*****************************/
    /*    Singleton Code          */
    /****************************/
    /**
     * Hold the intance of the class
     * 
     * @var self
     */
    protected static $_instance = null;      
   
    /**
     * Return the class instance
     */
    public static function getInstance() {
        if (!self::$_instance) {
            $class = get_called_class();
            self::$_instance = new $class;            
        } 
        
        return self::$_instance;
    }
    
    protected function checkInstance() {
        if (self::$_instance)
            die('Singleton class');
    }
    
    
    public function __construct() {                           
        $this->configData = acs_config::getInstance(); //All the config data is now accessible through this protected variable :)                
        $this->log = acs_log::getInstance();
        
        $this->cache = acs_cache::getCache($this->configData->cache['cache_type']);
    }
    
    /**
     * Return framework version
     */
    public function version() {
        return $this->configData->fw_name . ' ' . $this->configData->version;     
    }
    
    public function __toString() {
        return $this->version();        
    }
    
    
    /************************/
    /*    Models Code          */
    /************************/
	/**
	* @deprecated Use loadModels
	*/
    public function _loadmodels() {
		call_user_func_array(array($this, 'loadModels'), func_get_args());
        //$this->load
    }
	/**
    * This will allow me to load multiple models
    */
    public function loadModels() {
        for ($i = 0, $t = func_num_args(); $i < $t; $i++)
            $this->loadModel(func_get_arg($i));
    }         
    

    /**
    * @deprecated Use loadModel
    */
    public function _loadmodel($modelname) {        
        return $this->loadModel($modelname);
    }
    
    /**
     * Method to load the models
     * 
     * @author AntonioCS
     * @param string $modelname
     * @return instance of new model
     */
    public function loadModel($modelname) {        
        $m = basename($modelname); //just in case the model is in a subdirectory
         
        if (isset($this->_models[$m])) //If it's already loaded we just return the instance
            return $this->_models[$m];
            
        if (class_exists($m,false)) //We must check if the class exists (this can happen if I use the loadModel in a function in another place)
            return $this->createModel($m);
        
        $mpath = $this->getModelPath($modelname);
        
        if ($mpath) {                                   
            require($mpath);            
            if (class_exists($m,false))             
                return $this->createModel($m);            
            else 
                throw new acs_exception(MODEL_ERROR_NOMODELCLASS . ' ' . $m);            
        }
        else 
            throw new acs_exception(MODEL_ERROR_NOMODEL . ': ' . $modelname);
    }
    
    /**
    * Load a form module
    * This is a loadModel but the model name has a '_form' added to the ed
    * 
    * @param String $modelName
    */
    public function loadModelForm($modelName) {
        return $this->loadModel($modelname . '_form');
    }
    
    /**
     * Return the path to the model or null if there is no path
     * 
     * @param string $modelname
     * @param bool $checkSubdirName If this is true (default to true) and the path has not been found, the method will try one more time to get the correct path of the model
     */
    public function getModelPath($modelname, $checkSubdirName = true) {    
        $ext = $this->configData->common_extension;
        
        $mpath = $this->configData->model_dir . $modelname . '_Model.' . $ext;
        $acs_mpath = $this->configData->acs_model_dir . $modelname . '_Model.' . $ext;
                        
        if (file_exists($mpath)) 
            return $mpath;
                
        if (file_exists($acs_mpath))           
            return $acs_mpath;
        
        if ($checkSubdirName) { //if this is the first call and the path was not returned let's see if the model is in a subdir with it's own first part of the name 
            $modelDirname = current(explode('_',$modelname)); 
            
            $path = $modelDirname . '/' . $modelname; //if the model is in the dir auth and has the name auth_ident the $path will contain auth/auth_ident
            return $this->getModelPath($path, false);        
        } 
            
        return null;
    }
    
    /**
     * Return true of false depending on the existance of the model
     * 
     * @param string $model Path to model
     * @return bool
     */
    public function isModel($model) {
        return (bool)$this->getModelPath($model);
    }
    
    public function isModelForm($model) {
        return $this->isModel($model . '_form');
    }
    
    /**
    * Create the instance of the model, store it, call the init method (if it has one) and return the instance
    * 
    * @param string $m - Model class name
    * @return model instance
    */
    protected function createModel($m) {                
        $this->_models[$m] = new $m;                 
          
        //Call a default method on start up of model.
        //So I don't have to mess with the construct of the class in case it inherits from other classes
        if (method_exists($this->_models[$m],'init'))
            $this->_models[$m]->init();
                                
        return $this->_models[$m];            
    }
    
    /**
    * Retrieve model
    * 
    * @param string $mname
    */
    public function model($mname) {
        if (!empty($this->_models) && isset($this->_models[$mname])) //if it's a model    
            return $this->_models[$mname];  
            
        return null;                  
    }
    
    public function __get($varname) {   
        return $this->model($varname);
    }
    

    /**
    * @deprecated use isAjaxRequest
    * 
    */
    public function is_ajax_request() {
        return $this->isAjaxRequest();
    }
    
    /**
    * Method to determine if the request made was via ajax
    * 
    * (taken from MicroMVC)
    * 
    * NOTE: jQuery sets this
    *     
    */
    public function isAjaxRequest() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
    }
}
