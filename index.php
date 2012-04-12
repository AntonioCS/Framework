<?php 

/*
    !!!ACS Framework!!!

				  ''.;.                                     
				 ,.  ''";.;.                                 
				'"""'...::::..                               
			""...     '"   '":;                             
			   "'"...   ...  "";.                           
				  '':;. '"':...::..                         
					 '::.    '""":::.                       
			 '".......;:::;.      '::;.                     
					 """"'::;       '::.                     
					  ;"..::::..   ,.;:::                   
						  .;.;:::::::::::;.;.               
					  ,.:::::"""::::'  :::':::;...;.....""' 
					 :::"'       "::.   :;   '"'""""""'     
					,::'          :::   ::;      ..         
				   ,::'            :::  :::  ,..:::::::""'   
	   ..;:;      ,;:'             ::;;.::::::::::"""'       
	 ;;::"::;     ;:"              ;::::::::::::::           
   ,::""   :::   ;::              ;:::::::::::::::;::::     
  :::'      ::. ,::'             ;::::::::::::::::::"'       
  ::'       '::,;:'             ,:::""  ;:::::::::::  ,...   
 ;::         :::::          ...::::;.  ;::::::::::::::::'   
 :::         ::::'    ,.:..:::::::::::::::::::::::::::'     
 :::        .::::.  ;;:::::::::::::::::::::::::::::::;:  ... 
 ::'     ,;::::::::::::::::::::::::::::::::::::::::::::::::' 
 ':;    :;:""'::::::::::::::::::::::"":::::::::::::::::::"   
  ::;   :::  ,:::::::::::::"""::""',;;::::::::::::::::::::   
  ':::  '::..:::::::::::"'    "' .;;::::::::::::::::::::::. 
   '::;  '"::::::""  '':   .....;:::::::::::::::::::::::::: 
	 ::.   '""'::   ..    .:::::::::::::::::::::::::::::::: 
	 '::.         .;:;.::::::""    '"'""""':::::::::::::::: 
	  ':;.   ,;::::::::"""'                :::::::::::::::: 
	   '::   '::::::::'                    :::::::::::::::: 
		::    '":: ':;.                    :::::::::::::::: 
		::      '::  '""'                  :::::::::::::::: 
		;'       '':                     :::::::::::::::::: 
		:                               ;:::::::::::::::::: 
	   :'                              '""""""""""""""""""' 
	   " 
 

* Defining base path and common extension
* 
* This must be defined here so I can know the base path and what extensions the files have
* Fell free to change this to something outside apache's public html folder
* This will then be replaced in the config file 
* 
* NOTE: if the extension is not .php please also change in the .htaccess file
*/

error_reporting(E_ALL);
date_default_timezone_set('Europe/Lisbon'); //http://php.net/manual/en/timezones.php	

//In live version HARD CODE these constants
//Path for the base path
define('ROOTPATH','./');
//Path used for the applications folder and web folder
define('REALPATH',ROOTPATH); 
define('CONFIGPATH','./config/');

define('COMMON_EXTENSION', 'php'); //pathinfo(__FILE__, PATHINFO_EXTENSION));  

//require(CONFIGPATH . 'acs_config.' . COMMON_EXTENSION);
//require('./autoload.' . COMMON_EXTENSION);  


// Autoload process ---
//From http://www.php.net/manual/en/function.spl-autoload.php#92767
//Simple, clean and efficient!

//Add your class dirs to include path
//In live version HARD CODE these paths in php.ini if possible
set_include_path(       
    ROOTPATH . 'base/' . 
    PATH_SEPARATOR .
    ROOTPATH . 'base/elements/'.
    PATH_SEPARATOR .
    ROOTPATH . 'base/elements/form/' .
    PATH_SEPARATOR .
    ROOTPATH . 'base/helpers/' .
    PATH_SEPARATOR .
    get_include_path()
);
spl_autoload_extensions('.' . COMMON_EXTENSION);
//Use default autoload implementation
spl_autoload_register();


//$acs_init = new acs_init();
new acs_init();