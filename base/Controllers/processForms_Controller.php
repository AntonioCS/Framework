<?php


class processForms_Controller extends acs_controller {
	
	public function init() {
		$this->v->noshow();
	}
	public function index() {		
		helper_url::redirect($this->configData->uri);
	}
	
	public function _no_action($action) {		
		if ($this->isPost()) {
        
            //Must collect all parameters (action and data) because the model may be in a subdirectory
            $parms = implode('/',helper_url::getserverarg(2,true));
        
			/*			
			var_dump(
				$this->post->token->value != $this->session->getToken($this->post->token_name->value),
				$this->post->token->value,
				$this->session->getToken($this->post->token_name->value)
				);
			*/
			if (($this->post->token->isNull() || 
					$this->post->token_name->isNull() || 
						$this->post->token->value != $this->session->getToken($this->post->token_name->value)) && $this->configData->use_form_token) 
				die('TOKEN PROBLEM'); //TODO: Handle this better. Redirect to the form page with a msg or something
				
			$this->session->deleteToken($this->post->token_name->value);
            
			//try to load models with the same name to process the $_POST/$_GET data
			$this->loadformhandler($parms);			
		}
		else
			helper_url::redirect($this->configData->uri);		
	}	
	
	//private method to load model to correctly handle data
	private function loadformhandler($model) {
		$formmodel = $model . '_form';	
        
		if ($this->isModel($formmodel)) {
            $redirect = $this->loadmodel($formmodel)->process($this->post);                        
            
            
            if ($redirect) {
                //var_dump("teste1");
                helper_url::redirect($redirect);
            }
            else {                
                $this->msg()->setmsg($formmodel . '_pdata',$_POST);            
                helper_url::redirect(helper_url::referer());
            }
        }
        else {
            //TODO: Do something here (maybe load a generic model to just check the values)
        }
	}
}