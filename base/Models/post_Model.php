<?php

class post extends acs_data_handler {
    public function __construct() {
        parent::__construct();

        $this->setDataSource($_POST);
    }  
}