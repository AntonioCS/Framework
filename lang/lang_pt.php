<?php 

define('CONTROLLER_ERROR_NOCONTROLLER','O controlador especificado n�o existe');
define('CONTROLLER_ERROR_NOACTION','Esta ac��o n�o existe');
define('CONTROLLER_ERROR_NOINDEXACTION','Este controlador n�o tem index');
define('CONTROLLER_ERROR_NOPARENT','Este controlador deve herdar do controlador base');
define('CONTROLLER_ERROR_NOCLASS','Class chamada n�o existe');

define('EXCEPTION_CAUGHT','Excep��o encontrada: ');

define('AUTOLOAD_ERROR','Class base pedida n�o foi encontrada: ');

define('VIEW_ERROR_NOFILE','A \'view\' especificada n�o existe na directoria da \'view\'');
define('VIEW_ERROR_NOVIEWSET','N�o existe uma \'view\' definida');

define('MODEL_ERROR_NOMODEL','O modelo pedido n�o foi encontrado');
define('MODEL_ERROR_NOMODELCLASS','Class do modelo n�o foi encontrada');




define('SCAFFOLDING_FORM_SUCCESS','Dados introduzidos com sucesso');
define('SCAFFOLDING_FORM_EDIT_SUCCESS','Dados editados com sucesso');
define('SCAFFOLDING_FORM_ERROR','Foram detectados erros');
define('SCAFFOLDING_FORM_ERROR_FIELD_NOT_NULL','Este campo n�o pode ser nulo');
define('SCAFFOLDING_FORM_ERROR_EMAIL','Os parametros recebidos n�o se passaram a valida��o de email');
define('SCAFFOLDING_FORM_ERROR_PASSWORD','A password n�o pode ser nula');
define('SCAFFOLDING_FORM_ERROR_PASSWORD_CONF','A password de confirma��o n�o pode ser nula');
define('SCAFFOLDING_FORM_ERROR_PASSWORD_NO_MATCH','A password e a password de confirma��o tem que ser iguais');
define('SCAFFOLDING_FORM_ERROR_NUMERIC','Este campo so podera levar numeros');
define('SCAFFOLDING_FORM_ERROR_FILE','Tem que introduzir um ficheiro');

define('SCAFFOLDING_DELMSG_SUCCESS','Item apagado com sucesso');
define('SCAFFOLDING_DELMSG_FAILURE','Ocorreu um erro ao tentar apagar o item');

define('SCAFFOLDING_ERROR_NOTABLE','Scaffold Erro: N�o foi encontrada a tabela');

define('ACTIVERECORDING_ERROR_NOPRIKEY','N�o existe chave primaria');
define('ACTIVERECORDING_ERROR_NOSELID','N�o existe uma id escolhida');
define('ACTIVERECORDING_ERROR_NODATAID','A id escolhida n�o retorna dados');
define('ACTIVERECORDING_ERROR_NOSELDATA','N�o existem dados selecionados');
define('ACTIVERECORDING_ERROR_NOFIELD','Esse campo n�o existe');

//Erros da base de dados
define('DB_CONNERROR','Connec��o a base de dados falhou: ');
define('DB_NOCONNDEFINED','N�o existe uma liga��o a base de dados definida');
define('DB_NOTABLE','A tabela n�o existe');
//DataBase Management System Data 
define('DBMSD_NO_CONNECTION_TYPE','O tipo de liga��o n�o tem dados associados');
define('DBMSD_CONNECTION_ERROR','Erro ao tentar connectar');
define('DBMSD_CONNECTION_TYPE_NOT_SUPPORTED','O tipo de Sistema de Gestao de Bases de Dados n�o � suportado');

define('CONFIG_INVALID_PROPERTY','Propriedade invalidade do ficheiro de configura��es selecionado');

define('LOG_DIR_NOT_WRITTABLE','Impossivel escrever para directoria dos Logs');