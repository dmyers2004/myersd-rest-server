<?php
/*
Don Myers 2012
Open Software License v. 3.0 (OSL-3.0)
http://www.opensource.org/licenses/osl-3.0.php
*/
/* 
SetEnv MODE DEBUG
DEBUG, TEST, production (default)
*/
switch ($_SERVER['MODE']) {
	case 'DEBUG':
		error_reporting(E_ALL);
		ini_set('display_errors','On');
	break;
	case 'TEST':
		error_reporting(E_ALL & ~E_NOTICE);
		ini_set('display_errors','On');
	break;
	default:
		ini_set('display_errors','Off');	
}

define('RUNNING',true);

/* load our core object */
require('server/libraries/core.php');

/* create core super class & setup */
$core = new core();

/* find the rest model and run it! show 200 ok if something else hasn't stopped it */
$core->run()->ok();
