<?php
define( "baseAppFolder",	"api/public/");
define( "baseAdminFolder",	baseAppFolder . "backend/" );
define( "baseSiteFolder", 	baseAppFolder . "");
/*Agrega enlaces directos para ingreso a carpetas */
define( "pathToRoot",   	realpath(dirname( __FILE__ ) ) . '/../..' );
define( "pathToLib",   		realpath(pathToRoot) . '/_sys/lib/' );
define( "rootUpload",   	realpath(pathToRoot) . '/upload/' );
define( "pathToView",		realpath(pathToRoot) . '/_sys/lib/view/' );
define( "pathToController",	realpath(pathToRoot) . '/_sys/lib/controller/' );
define( "pathToTemplate", 	realpath(pathToRoot) . '/_sys/templates/' );
define( "pathToLog",		realpath(pathToRoot) . '/_sys/dblog/' );
/*resumen de urls para concatenar al enlace*/
define( "baseURL",     		"http://" . $_SERVER['SERVER_NAME'] . "/" . baseAppFolder );
define( "baseAdminURL",		"http://" . $_SERVER['SERVER_NAME'] . "/" . baseAdminFolder );
define( "baseSiteURL",		"http://" . $_SERVER['SERVER_NAME'] . "/" . baseSiteFolder );
define( "uploadURL",		baseURL . "upload/");
?>