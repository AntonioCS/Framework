<?php

class counter extends acs_activerecord {
    private $_s = null;

    public function __construct() {
        parent::__construct();

        $this->fetch_counter();
        $_s = acs_session::getInstance();

        if (!$_s->userVisited) {
        	$this->counter++;
        	$_s->userVisited = true;
		}



        $this->v->counter = $this->counter->counter;
        $this->counter->save();
    }       
}