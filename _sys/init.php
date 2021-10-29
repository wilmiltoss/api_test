<?php
ini_set('display_errors', 1);
error_reporting(E_ALL ^E_NOTICE);
session_start();

require_once    ( "config/sys.config.php" );
require_once    ( pathToLib . "/functions.lib.php");
require_once    ( pathToLib . "/core.lib.php" );

Folder::Load    ( pathToLib . "/controller/" );
Folder::Load    ( pathToLib . "/plugins/" );
?>