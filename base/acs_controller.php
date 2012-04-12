<?php 

abstract class acs_controller extends acs_base {
	
	/**
	* This will hold the acs_msg class
	* 
	* @var acs_msg
	*/
	private $_msg = null;
	
	/**	 
	 * This will hold the view of the controller if there is a view named controller
	 * 
	 * @var acs_view
	 */
	
    public $view = null;
	public $v = null; //the view
    
    /**
    * The view of the action if there is a view named controller_action
    * 
    * @var acs_view
    */
    public $view_action = null;
    
    /**
    * Controller name
    * 
    * @var string
    */
    public $name = null;
    
    /**
    * name of the called action
    * 
    * @var string
    */
    public $action_name = null;

	/**
	 * This will hold the session class
	 * 
	 * @var acs_session
	 */
	public $session = null; //Session variable
	
    /**
    * Property containing the path to the current page
    * 
    * @var string
    */
    protected $_self_url = null;
    /**
    * Property containing the path to the current page without the page
    * 
    * @var string
    */
    protected $_self_url_path = null; 
    
    /**
    * Determine if the post is loaded 
    * 
    * @var bool
    */
    private $_isPost = false;
	
    /**
    * Control if the beforeRender method is called
    * 
    * @var bool
    */
    private $_callBeforeRender = true;
    
    /**
    * Control if the afterRender method is called
    * 
    * @var bool
    */
    private $_callAfterRender = true;
	
	/**
	 * 
	 * Construct
	 */
	public function __construct($controllerview = null) {
		parent::__construct();

		$this->v = ($controllerview ? $controllerview : new acs_view());
        $this->view = &$this->v;
		
		$this->session = acs_session::getInstance();        

        $this->name = implode('_',array_splice(explode('_',get_class($this)),0,-1));
        
		$this->init();
		
        $this->_self_url = helper_url::getURL();
        $this->_self_url_path = dirname($this->_self_url) . '/';
        
        if (!empty($_POST)) {
            $this->loadmodel('post');
            $this->_isPost = true;            
		}
	}		
	
	/**
	* Same as in zend
	* Not abstract because the child class does not have to implement this, it will just inherit this one which does nothing
	* 
	*/
	public function init() {}

	/**
	* Method to be called by the acs_router class before the view is called
	* 
	*/
	public function beforeRender() {}	
    
    /**
    * Method to be called by the acs_router class after the view is rendered
    * 
    */
    public function afterRender() {}
	
	
	/**
	 * Method that the child class will have to have
	 *
	 */
	abstract function index();
    
    /**
    * Stop the beforeRender method from executing
    * 
    */    
    public function stopBeforeRender() {
        $this->_callBeforeRender = false;
    }
	
    /**
    * Allow execution of the beforeRender method 
    * 
    */        
    public function enableBeforeRender() {
        $this->_callBeforeRender = true;
    }
    
    /**
    * State of the beforeRender
    * 
    */
    public function statusBeforeRender() {
        return $this->_callBeforeRender;
    }
    
    /**
    * Stop the afterRender method from executing
    * 
    */    
    public function stopAfterRender() {
        $this->_callAfterRender = false;
    }
    
    /**
    * Allow execution of the afterRender method 
    * 
    */        
    public function enableAfterRender() {
        $this->_callAfterRender = true;
    }
    
    /**
    * State of the beforeRender
    * 
    */
    public function statusAfterRender() {
        return $this->_callAfterRender;
    }
    

	/**
	* Create a new acs_view, assign the name to a public property of the controller class and return the instance
	* 
	* @param string $viewname Name for the new acs_view
	* 
	* @return acs_view
	*/
	public function newview($viewname = null) {
		if (!$viewname) //incase I just don't nead a name and just the view
			return new acs_view();
	
		if (!isset($this->$viewname)) 
			$this->$viewname = new acs_view();			
		
		return $this->$viewname;	
	}
	
	/**
	 * Overload of the get operation. This way it will refer to a private array
	 * @param mixed $varname
	 * @return data of the variable
	 */
	public function __get($varname) {	
		$p = parent::__get($varname);
		if ($p)
			return $p;

		if ($varname[0] == 'v') { //same thing as the preg_match, but much much faster
			$this->$varname = new acs_view();
			return $this->$varname;			
		}	
	}
	
	/**
	* Method that will return the acs_msg class
	* 
	* @return acs_msg
	* 	
	*/
	public function msg() {
		if (!$this->_msg)
			$this->_msg = acs_msg::getInstance();//new acs_msg();
		
		return $this->_msg;
	}
	
	/**
	* Getter for the _isPost property which is a bolean value, so this will either be true if there is $_POST and false if there isn't
	* 
	*/
	protected function isPost() {
		return $this->_isPost;	
	}
}
