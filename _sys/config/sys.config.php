<?php
/*zona horaria*/
date_default_timezone_set('America/Belem');

/**/
define( "isLocal",     true ); 			/*si se trabaja de manera local*/
define( "sysName",      "API - Retail" );    	/*nombre del cms*/
define( "sysVersion",   "1.0.1");
define( "domainName",	"api.retail.com.py");
define( "logEnabled", 	false);	    	/*habilita log de consultas sql*/

/*encriptacion para c/ conexion se cambia*/
define( "encryptionKey", "@qAdf2asdfaw3423!23&(?0"); 

/**/
$domainName = explode('.', domainName);
$domainName = $domainName[0];
$sessionSub = strtolower(metaphone($domainName));

define( "authToken",		strrev(md5(sha1(session_id()))));

define( "adminLogin",		"_adl_" . $sessionSub );
define( "userLogin",		"_usl_" . $sessionSub );
define( "messageVar", 		"_msgflash_" . $sessionSub );

/*mensajes alertas*/
define( "MESSAGE_ERROR",		"ERROR" );
define( "MESSAGE_SUCCESS",		"SUCCESS" );
define( "MESSAGE_WARNING",		"WARNING" );
define( "MESSAGE_INFORMATION",	"INFORMATION" );
define( "MESSAGE_QUESTION",		"QUESTION" );

/* requerimientos para la conexion a la bd*/
require_once( 'sys.paths.php' ); //carpetas a concatenar
require_once( 'sys.db.php' );    //parametros para la bd
require_once( 'sys.smtp.php' ); //vacio no se usa

define("granUnion",  array(53,65,37,42,51,63,52,75,56,39,55,38,54,27));

?>