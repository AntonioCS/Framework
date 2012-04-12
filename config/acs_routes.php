<?php 

/*
* 
* Routes table
* 
* Key name = Match pattern
* Key value = new route
* 
* 
*/

$acs_routerTable = array(
	'/^$/' => 'index', //default controller
    
	//'/^((?!(auth|admin)).+)+$/' => 'index/$1', //prevent the auth from triggering this
	//'/^(.+)$/'  => 'index/$1',
);