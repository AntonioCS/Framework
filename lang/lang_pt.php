<?php 

define('CONTROLLER_ERROR_NOCONTROLLER','O controlador especificado nуo existe');
define('CONTROLLER_ERROR_NOACTION','Esta acчуo nуo existe');
define('CONTROLLER_ERROR_NOINDEXACTION','Este controlador nуo tem index');
define('CONTROLLER_ERROR_NOPARENT','Este controlador deve herdar do controlador base');
define('CONTROLLER_ERROR_NOCLASS','Class chamada nуo existe');

define('EXCEPTION_CAUGHT','Excepчуo encontrada: ');

define('AUTOLOAD_ERROR','Class base pedida nуo foi encontrada: ');

define('VIEW_ERROR_NOFILE','A \'view\' especificada nуo existe na directoria da \'view\'');
define('VIEW_ERROR_NOVIEWSET','Nуo existe uma \'view\' definida');

define('MODEL_ERROR_NOMODEL','O modelo pedido nуo foi encontrado');
define('MODEL_ERROR_NOMODELCLASS','Class do modelo nуo foi encontrada');




define('SCAFFOLDING_FORM_SUCCESS','Dados introduzidos com sucesso');
define('SCAFFOLDING_FORM_EDIT_SUCCESS','Dados editados com sucesso');
define('SCAFFOLDING_FORM_ERROR','Foram detectados erros');
define('SCAFFOLDING_FORM_ERROR_FIELD_NOT_NULL','Este campo nуo pode ser nulo');
define('SCAFFOLDING_FORM_ERROR_EMAIL','Os parametros recebidos nуo se passaram a validaчуo de email');
define('SCAFFOLDING_FORM_ERROR_PASSWORD','A password nуo pode ser nula');
define('SCAFFOLDING_FORM_ERROR_PASSWORD_CONF','A password de confirmaчуo nуo pode ser nula');
define('SCAFFOLDING_FORM_ERROR_PASSWORD_NO_MATCH','A password e a password de confirmaчуo tem que ser iguais');
define('SCAFFOLDING_FORM_ERROR_NUMERIC','Este campo so podera levar numeros');
define('SCAFFOLDING_FORM_ERROR_FILE','Tem que introduzir um ficheiro');

define('SCAFFOLDING_DELMSG_SUCCESS','Item apagado com sucesso');
define('SCAFFOLDING_DELMSG_FAILURE','Ocorreu um erro ao tentar apagar o item');

define('SCAFFOLDING_ERROR_NOTABLE','Scaffold Erro: Nуo foi encontrada a tabela');

define('ACTIVERECORDING_ERROR_NOPRIKEY','Nуo existe chave primaria');
define('ACTIVERECORDING_ERROR_NOSELID','Nуo existe uma id escolhida');
define('ACTIVERECORDING_ERROR_NODATAID','A id escolhida nуo retorna dados');
define('ACTIVERECORDING_ERROR_NOSELDATA','Nуo existem dados selecionados');
define('ACTIVERECORDING_ERROR_NOFIELD','Esse campo nуo existe');

//Erros da base de dados
define('DB_CONNERROR','Connecчуo a base de dados falhou: ');
define('DB_NOCONNDEFINED','Nуo existe uma ligaчуo a base de dados definida');
define('DB_NOTABLE','A tabela nуo existe');
//DataBase Management System Data 
define('DBMSD_NO_CONNECTION_TYPE','O tipo de ligaчуo nуo tem dados associados');
define('DBMSD_CONNECTION_ERROR','Erro ao tentar connectar');
define('DBMSD_CONNECTION_TYPE_NOT_SUPPORTED','O tipo de Sistema de Gestao de Bases de Dados nуo щ suportado');

define('CONFIG_INVALID_PROPERTY','Propriedade invalidade do ficheiro de configuraчѕes selecionado');

define('LOG_DIR_NOT_WRITTABLE','Impossivel escrever para directoria dos Logs');