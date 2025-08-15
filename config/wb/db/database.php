<?php

use App\php\wb_database\WB_Database;
// Configurazione database

$serverconfigarraydb = require(controllerDIR_BASE.'/config/wb/server/serverconfig.php');

$CONF_DB_HOST = $serverconfigarraydb['databasehost'];
$CONF_DB_NAME = $serverconfigarraydb['db'];
$CONF_DB_TYPE = 'mysql';
$CONF_DB_USERNAME = $serverconfigarraydb['user'];
$CONF_DB_PASSWORD = $serverconfigarraydb['password'];

define('configdatabaseDIR_BASE', dirname(dirname(dirname(dirname( __FILE__ )))).'/');

//$CONF_DB_STRING = $CONF_DB_TYPE.':host='.$CONF_DB_HOST.';dbname='.$CONF_DB_NAME;
require_once(configdatabaseDIR_BASE.'src/php/wb_database/WB_Database.php');
require_once(configdatabaseDIR_BASE.'src/php/wb_database/WB_TableField.php');

$database = new WB_Database(
	$CONF_DB_TYPE,
	$CONF_DB_HOST,
	$CONF_DB_NAME,
	$CONF_DB_USERNAME,
	$CONF_DB_PASSWORD);
//require_once($_SERVER['DOCUMENT_ROOT'].'/config/database_structure.php');
//$database->setStructure( $database_structure );


//require_once($_SERVER['DOCUMENT_ROOT'].'/config/db_structure.php');
//$funzione = 'load_database_structure()';
//$database->loadStructure( 'load_database_structure()' );
//$database->loadStructure( '/config/db_structure.php' );
//$database->loadStructure( '/config/databaseStructure/database_woodwork.php' );
$database->loadStructure( '/config/wb/db/db_structure_default.php' );
?>
