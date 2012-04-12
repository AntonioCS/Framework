<?php

$acs_confdata['fw_name'] = 'Acs Framework';
$acs_confdata['version'] = 1.1;

//Set the language extension to use
$acs_confdata['language'] = 'pt';

//If you using mod_rewrite set  to true
$acs_confdata['using_modrewrite'] = true;

//in case mod_rewrite is no being used
//Query String Controller parameter
$acs_confdata['qs_controller'] = 'c';
//Query String Action parameter   
$acs_confdata['qs_action'] = 'a';
//Query String Data parameter   
//The data should then be separated by a :
//Ex: data1:data2:data3
//$acs_confdata['qs_data'] = 'd'; 

//NOTE if the mod write is not being used the rest of the parameters are to be passed normally
//		Like: x=1&y=weew etc...
//So forget about the qs_data

$acs_confdata['cache_config'] = false;


//Database Management System
//Better name for this -> Database Management System Data 
//Database Settings
//Better name for this -> Database Profile
$acs_confdata['dbp'] = array(
                'user' => array(
                				'type' => 'mysql',
                				'server' => 'acs'
							),                
				);				
$acs_confdata['dbp_default'] = 'user';

//The databases
$acs_confdata['dbs'] = array(
						'mysql' => array(
							'acs' => array(
											'host' => 'localhost',
											'user' => 'acsframework',
											'pass' => 'acsframework',
											'database' => 'acsframework',                            
										)
						)
					);

//This is for the active record class which will call save after an update, delete and insert if this is true
$acs_confdata['dbmsd_autocommit'] = false;
//To add LIMIT 1 to the update queries
$acs_confdata['dbmsd_update_limit'] = true;

//In case you have extension like php5 or something (this is automatically done in the index but you can overwrite it here
//for most of the files but not for the acs_config and acs_config_data)
//Leave this blank to be auto set 
$acs_confdata['common_extension'] = '';        

//Name of the defaul file served by the web browser (normally index). Will be set to index plus the common extension
//If the file you will be serving is not index, also change the .htaccess file
$acs_confdata['indexfile'] = '';

//For the view file to be parsed for Western Characters (for example: á will be set as &aacute;)
$acs_confdata['parseWesternCharacters'] = false;
                                          
############ Directory Setup ############   
//Relative to the root
$acs_confdata['basepath'] = ROOTPATH;
$acs_confdata['realpath'] = REALPATH;
$acs_confdata['base_dir'] = $acs_confdata['basepath'] . 'base/';

$acs_confdata['pathroutes'] = $acs_confdata['realpath'] . 'config/acs_routes.' . COMMON_EXTENSION;

$acs_confdata['reset_uri'] = true; //prevent the uri settings from being cached

$acs_confdata['helpers_dir'] = $acs_confdata['basepath'] . 'helpers/';
$acs_confdata['applications_dir'] = $acs_confdata['realpath'] . 'applications/';
$acs_confdata['language_dir'] = $acs_confdata['realpath'] . 'lang/'; 
$acs_confdata['cache_dir'] = $acs_confdata['realpath'] . 'cache/';
$acs_confdata['lib_dir'] = $acs_confdata['realpath'] . 'lib/';     
$acs_confdata['scaffold_dir'] = $acs_confdata['basepath'] . 'scaffolding/';
$acs_confdata['web_dir'] = $acs_confdata['realpath'] . 'web/'; 	
//this will be set to example.com/web/ - will be set in the internals of the config file
$acs_confdata['web_dir_url'] = 'web/';

//frameworks controllers, models and views
$acs_confdata['acs_controller_dir'] = $acs_confdata['base_dir'] . 'Controllers/';
$acs_confdata['acs_model_dir'] = $acs_confdata['base_dir'] . 'Models/';
$acs_confdata['acs_view_dir'] = $acs_confdata['base_dir'] . 'Views/';

//Relative to the applications directory (at least by default, feel free to change)
//users controllers, models and views
$acs_confdata['controller_dir'] = $acs_confdata['applications_dir'] . 'Controller/';
//To share logic among controllers
$acs_confdata['maincontroller'] = $acs_confdata['controller_dir'] . 'MainController';
$acs_confdata['model_dir'] = $acs_confdata['applications_dir'] . 'Model/';
$acs_confdata['view_dir'] = $acs_confdata['applications_dir'] . 'View/';

//Relative to the web directory	(at least by default, feel free to change)		
$acs_confdata['css_dir'] = $acs_confdata['web_dir'] . 'css/';
$acs_confdata['js_dir'] = $acs_confdata['web_dir'] . 'js/';
$acs_confdata['images_dir'] = $acs_confdata['web_dir'] . 'images/';
$acs_confdata['flash_dir'] = $acs_confdata['web_dir'] . 'flash/';
$acs_confdata['misc_dir'] = $acs_confdata['web_dir'] . 'misc/';

//will be set in the internals of the config file
$acs_confdata['css_dir_url'] = $acs_confdata['web_dir_url'] . 'css/';
$acs_confdata['js_dir_url'] = $acs_confdata['web_dir_url'] . 'js/';
$acs_confdata['images_dir_url'] = $acs_confdata['web_dir_url'] . 'images/';
$acs_confdata['flash_dir_url'] = $acs_confdata['web_dir_url'] . 'flash/';
$acs_confdata['misc_dir_url'] = $acs_confdata['web_dir_url'] . 'misc/';
		
############ Directory Setup End ############ 

//Check or don't check token
$acs_confdata['use_form_token'] = true;

############# Caching ################

/*
    ttl possible values
    Values to be entered in format: <number>[<time>]
    Being <number> 1 to N
    Being <time>:
        s = seconds (default)
        m = minutes
        h = hours
        d = days
        w = weeks
        m = months
        y = years (not likely used but still valid)        
        
    Note: If there is no 'time' parameter seconds will be assumed    
    
        
    Examples:        
         = '1d'; //Set cached data to be keept for one day
         = '120s'; //Set cached data to be keept for 120 seconds (2 minutes)
         = '2m'; //Same as the above but using the 'm' time formater
         = '15d'; //Set cached data to be keept for 15 days    
*/


$acs_confdata['cache'] = array(
								//Type of cache to use
								'cache_type' => 'file',
								//'cache_type' => 'memcached',

								//Cache settings
								'settings' => array(
									'file' => array (                                	
	                                    'dir' => $acs_confdata['cache_dir'],                                                                    
	                                    'ttl' => '5h' //ttl = time to live
	                                ),
	                                
	                                'memcached' => array(                                                  
	                                                    'servers' => array(     
		                                							'host' => 'localhost',                            														
		                                                    		'port' => 11211,	                                                    		
		                                							'weight' => 10,	                                                    		
	                                               				),
	                                               		'compress' => true,   
	                                               		'ttl' => '5h'                                           
	                                              )
                                )
);

$acs_confdata['errorShow'] = true;

############ Logging/Debug options ############

$acs_confdata['logsDir'] = $acs_confdata['realpath'] . 'logs/';

$acs_confdata['accessLogFile'] = 'access.log';
$acs_confdata['errorLogFile'] = 'error.log';

$acs_confdata['debugLofFile'] = 'debug.log';

$acs_confdata['logAccess'] = true;
$acs_confdata['logErrors'] = true;


$acs_confdata['Debug'] = true;
$acs_confdata['DebugMsgsOutput'] = false;
$acs_confdata['DebugToLogFile'] = false;

//It will call the console.log firebug function (in a try catch)
$acs_confdata['DebugToFirebug'] = false;

//auth model password salt (please change this)
//Use this generator -> http://www.random.org/strings/
$acs_confdata['salt'] = 'fjosiajr48rjf4r03rwefj40wer40r34';

//Router
$acs_confdata['log_possibleviews'] = true;

$acs_confdata['log_routes'] = true;