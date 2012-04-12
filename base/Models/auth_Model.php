<?php

class auth extends acs_model {	
    
    
    
    private $_auth_config =  array(
        'encryption' => 'sha256', //See registered hashing algorithms for the hash function

        'table_name' => 'users',
        'table_field_user' => 'user',
        'table_field_pass' => 'password',

        'post_field_user' => 'auth_Username',
        'post_field_pass' => 'auth_Password',

        'layout' => 'auth/auth',

        'controller' => 'auth',
        
        'minsizepassword' => 6,
        
        //how many times can the user get the username or password wrong
        'attempts' => 0, //0 = infinte
        
        //when the user exceeds the number of tries (not called if attempts is 0)
        //'overfail'
    );
    
    
    
    
    //--- Setters ---
    /**
    * Setter for the table where the users reside
    * 
    * @param string $table
    */
    public function setTableName($table) {
        if ($this->isTable($table)) {
            $this->_auth_config['table_name'] = $table;
        }
        else
            die('AuthModel: No such table');                    
    }
    /**
    * 
    * 
    * @param mixed $userfield
    */
    public function setTableFieldUser($userfield) {
        $this->_auth_config['table_field_user'] = $userfield;
    }
    /**
    * 
    * 
    * @param mixed $passfield
    */
    public function setTableFieldPass($passfield) {
        $this->_auth_config['table_field_pass'] = $passfield;
    }
    /**
    * 
    * 
    * @param string $userfield
    */
    public function setPostFieldUser($userfield) {
        $this->_auth_config['post_field_user'] = $userfield;
    }
    /**
    * 
    * 
    * @param string $passfield
    */
    public function setPostFieldPass($passfield) {
        $this->_auth_config['table_field_pass'] = $passfield;
    }
    /**
    * 
    * 
    * @param string $layout
    */
    public function setLayout($layout) {
        $this->_auth_config['layout'] = $layout;
    }
    /**
    * 
    * 
    * @param string $controller
    */
    public function setController($controller) {
        $this->_auth_config['controller'] = $controller;
    }
    //public function setMinPass($minpass) {
    //}
    
    //--- Getters ---
    
    private function getTableName() {
        return $this->_auth_config['table_name'];
    }                               
    private function getTableFieldUser() {
        return $this->_auth_config['table_field_user'];
    }
    private function getTableFieldPass() {
        return $this->_auth_config['table_field_pass'];
    }    
    private function getPostFieldUser() {
        return $this->_auth_config['post_field_user'];
    }
    private function getPostFieldPass() {
        return $this->_auth_config['table_field_pass'];
    }
    private function getLayout() {
        return $this->_auth_config['layout'];
    }
    private function getController() {
        return $this->_auth_config['controller'];
    }
    
    
    
    
	
	private $_userData = null;
	
    /*
	public $salt;
	
	public $encryption;
	
	public $table_name;
	public $table_field_user;
	public $table_field_pass;
	
	public $post_field_user;
	public $post_field_pass;
	
	public $layout;		
	
	public $controller;
	
	public $minsizepassword;
    */
	
	public function init() {
		//get the session instance
		$this->getSession();				
					
		$auth = null; 
		//check if data has been already set
        
		if (!$this->_s->auth_Data) {
			$auth_data = acs_config::getInstance()->auth_data;
			$auth_data['salt'] = acs_config::getInstance()->salt;            
			$this->_s->auth_Data = $auth_data;
		}
		else 
			$auth_data = $this->_s->auth_Data;
            
        $auth_data['salt'] = acs_config::getInstance()->salt;
			
		
		/*	
		$this->salt = $auth_data['salt'];
		
		$this->encryption = $auth_data['encryption'];
		
		$this->table_name = $auth_data['table_name'];
		$this->table_field_user = $auth_data['table_field_user'];
		$this->table_field_pass = $auth_data['table_field_pass'];
		
		$this->post_field_user = $auth_data['post_field_user'];
		$this->post_field_pass = $auth_data['post_field_pass'];
		
		$this->layout = $auth_data['layout'];
				
		$this->controller = $auth_data['controller'];
		
		$this->minsizepassword = $auth_data['minsizepassword'];
		
		$this->change_table($this->table_name);
        */
	}
	
	
	/**
	* Method to check if the user is logged in.
	* When this is called all settings must have been set
	* 
	*/
	public function checkUserLoggedIn() {		
		//first time method is called. Let's keep the data (the salt, encryption etc...)		 
		if (!$this->_s->auth_logged) {
			if (!$this->_s->auth_Data)       
				$this->_s->auth_Data = serialize($this->_data); 
				
			
			$this->_s->auth_redirectTo = helper_url::getURL();
					   
			helper_url::redirect('auth');
		}
		else { 
			unset($this->_s->auth_Data);
			return true;
		}
	}
	
	public function createLogin($setUserData = true) {		
		$this->_s->auth_logged = (($setUserData) ? (object)$this->_userData : true);
	}
	
	public function logout() {
		unset($this->_s->auth_logged);
		unset($this->_s->auth_redirectTo);	
		
		helper_url::refresh();
	}
	
	public function redirectToStart() {
		if ($this->_s->auth_redirectTo) 			
			helper_url::redirect($this->_s->auth_redirectTo);			
		return false;	
		
		//TODO: Add a default controller to go to when the user is logged in
	}	
	
	/**
	* Methodo to check if the data supplied is correct
	* 
	* @param post $data Post model data
	* @return mixed It will either be an array containg errors or true
	*/
	public function verify($post) {
		$errors = array();
		$username_field = $this->getPostFieldUser();
		$pass_field = $this->getPostFieldPass();
		
		if ($post->$username_field->isNull()) 
			$errors[] = 'No username given';
					
		if ($post->$pass_field->isNull())
			$errors[] = 'No password given';
				
		if (!empty($errors))
			return $errors;
			
								
		$pass = $this->encrypt($post->$pass_field);
		$this->change_table($this->table_name);
		
		$this->fetch('*',"$this->table_field_user = '" . $post->$username_field ."' AND $this->table_field_pass = '$pass'");
		
		if (!$this->numrows())
			return 'No such username or wrong password';
		
		//If all went well save the data and return true
		$this->_userData = $this->fetch_result();
		return true;		
	}
	
	/**
	* Return the retrieved data stored in the _userData property
	* 
	*/
	public function getUserData() {
		return ($this->_userData);	
	}
	
	private function encrypt($pass) {
	   return hash($this->encryption,$pass . $this->salt);	
	}
	
	public function addUser($username, $password) {
		if (strlen($password) >= $this->minsizepassword) {
			$password = $this->encrypt($password);			
		}
		else
			return 'Password must be bigger than ' . $this->minsizepassword;
			
		//TODO: Verify if there is already a user with this username
		return $this->insert(
						array(
								$this->table_field_user => $username,
								$this->table_field_pass	=> $password
							)
						)->save();						
	}
	
	public function delUser($username) { 	}
	
	
	public function editUser($username,$newusername,$newpassword) {}	
}