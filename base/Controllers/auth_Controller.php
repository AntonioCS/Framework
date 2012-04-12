<?php


class auth_Controller extends acs_controller {
    
    public function init() {
        $this->loadmodel('auth');
    }

    public function index() {        
            
        //$this->v->loadview($this->auth->layout);
        $this->view->loadview($this->auth->getLayout());
        
        //$this->v->title = $this->auth->title;
        //$this->v->processPath = $this->auth->process_path;
        
        if ($this->msg()->getmsg('auth_error'))
            $this->v->auth_error = $this->msg()->getmsg_clear('auth_error');    
    }
}
/*
class __auth_Controller extends acs_controller {
	
	public function init() {
		$this->loadmodel('auth');
	}
	
	public function index() {		
			
		$this->v->loadview($this->auth->layout);
		$this->v->title = $this->auth->title;
		$this->v->processPath = $this->auth->process_path;
		
		if ($this->msg()->getmsg('auth_error'))
			$this->v->auth_error = $this->msg()->getmsg_clear('auth_error');	
	}
	
	public function process() {
		$this->v->noshow();
		
		if ($this->isPost()) {
			$res = $this->auth->verify($this->post);
			
			if ($res === true) {
				$this->auth->createLogin();
				$this->auth->redirectToStart();				
			}
			else {
				$this->msg()->setmsg('auth_error','Username ou password invalidos');			
			}						
		}
										
		helper_url::redirect('auth');		
	}	
} */