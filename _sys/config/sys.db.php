<?php
# Parametros de conexion MySQL   conexion local : conexion externa
define('DBUser',    isLocal ? 'root'      		: 	'root'				);
define('DBPass',    isLocal ? ''    		    : 	'WWWd3v3l'			);
define('DBHost',    isLocal ? '127.0.0.1' 		: 	'localhost'			);
define('DBName',    isLocal ? 'bitacora'  	 	: 	'bitacora'			);
//define('DBName',    isLocal ? 'facturacion'  	: 	''			);


?>