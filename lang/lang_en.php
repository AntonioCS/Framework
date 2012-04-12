<?php 

define('CONTROLLER_ERROR_NOCONTROLLER','Specified controller does not exist');
define('CONTROLLER_ERROR_NOACTION','Action does not exist');
define('CONTROLLER_ERROR_NOINDEXACTION','This controller does not have an index');
define('CONTROLLER_ERROR_NOPARENT','This controller must inherit from the base controller');
define('CONTROLLER_ERROR_NOCLASS','Class called does not exist'); 
                               
define('EXCEPTION_CAUGHT','Caught exception: ');

define('AUTOLOAD_ERROR','Missing base class: ');

define('VIEW_ERROR_NOFILE','The specified view file does not exist in the view directory');
define('VIEW_ERROR_NOVIEWSET','There is no view set');

define('MODEL_ERROR_NOMODEL','The model requested was not found');
define('MODEL_ERROR_NOMODELCLASS','No model class found');



define('SCAFFOLDING_FORM_SUCCESS',''); //'Dados introduzidos com sucesso');
define('SCAFFOLDING_FORM_EDIT_SUCCESS',''); //'Dados editados com sucesso');
define('SCAFFOLDING_FORM_ERROR',''); //'Foram detectados erros');
define('SCAFFOLDING_FORM_ERROR_FIELD_NOT_NULL',''); //'Este campo não pode ser nulo');
define('SCAFFOLDING_FORM_ERROR_EMAIL',''); //'Os parametros recebidos não se passaram a validação de email');
define('SCAFFOLDING_FORM_ERROR_PASSWORD',''); //'A password não pode ser nula');
define('SCAFFOLDING_FORM_ERROR_PASSWORD_CONF',''); //'A password de confirmação não pode ser nula');
define('SCAFFOLDING_FORM_ERROR_PASSWORD_NO_MATCH',''); //'A password e a password de confirmação tem que ser iguais');
define('SCAFFOLDING_FORM_ERROR_NUMERIC',''); //'Este campo so podera levar numeros');
define('SCAFFOLDING_FORM_ERROR_FILE',''); //'Tem que introduzir um ficheiro');

define('SCAFFOLDING_DELMSG_SUCCESS','Successfully deleted item');
define('SCAFFOLDING_DELMSG_FAILURE','An error ocurred while trying to delete the item');

define('SCAFFOLDING_ERROR_NOTABLE','Scaffold Error: Table not found');
                                    
define('ACTIVERECORDING_ERROR_NOPRIKEY','There is no primary key');
define('ACTIVERECORDING_ERROR_NOSELID','There is no id choosen');
define('ACTIVERECORDING_ERROR_NOSELDATA','There is no data selected');
define('ACTIVERECORDING_ERROR_NODATAID','The choosen id does not return data');
define('ACTIVERECORDING_ERROR_NOFIELD','This field does not exist');

//Data base errors
define('DB_CONNERROR','Connection to database failed: ');
define('DB_NOCONNDEFINED','No database connection defined');
define('DB_NOTABLE','The table does not exist');
//Database Management System
define('DBMSD_NO_CONNECTION_TYPE','The connection type does not any data associated with it'); 
define('DBMSD_CONNECTION_ERROR','Error while trying to connect');
define('DBMSD_CONNECTION_TYPE_NOT_SUPPORTED','The Database Managment System type is not supported');

define('CONFIG_INVALID_PROPERTY','Invalid property from config file selected');

define('LOG_DIR_NOT_WRITTABLE','Log dir is not writeable');