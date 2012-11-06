<?php
/* Settings (please configure) */
$config['WEB_DOMAIN'] = 'http://localhost.myrestserver'; /* with http:// and NO trailing slash */
$config['WEB_FOLDER'] = '/'; /* with trailing slash */

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

/* do all your auto includes here */
/* require(); */

/* error / 404 handler - you can point this to your own function */
set_exception_handler('error_handler');

/* Session */
session_start();

/* Anything you want included as Default Data */
$data['sitename'] = 'Web Site Template';

/* ALL DONE - NOTHING ELSE BELOW NEEDS TO CHANGE */

/* Normal config vars */
$config['BASE_URL'] = $config['WEB_DOMAIN'].$config['WEB_FOLDER'];
$config['APP_PATH'] = 'app/'; /* with trailing slash */
$config['CONTROLLER_PATH'] = 'app/controllers/'; /* with trailing slash */
$config['VIEW_PATH'] = 'app/views/'; /* with trailing slash */
$config['MODEL_PATH'] = 'app/models/'; /* with trailing slash */
$config['LIB_PATH'] = 'app/libraries/'; /* with trailing slash */

/* get the url pieces */
$config['SEGS'] = explode('/',trim(urldecode(substr(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH),strlen($config['WEB_FOLDER']))),'/'));

/* if they didn't include a model use the default */
$config['CONTROLLER'] = (!empty($config['SEGS'][0])) ? strtolower(array_shift($config['SEGS'])) : 'main';
$config['FUNCTION'] = (!empty($config['SEGS'][0])) ? strtolower(array_shift($config['SEGS'])) : 'index';
$config['METHOD'] = ucfirst(strtolower($_SERVER['REQUEST_METHOD']));

/* does the controller file exist? */
if (!file_exists($config['CONTROLLER_PATH'].'/'.$config['CONTROLLER'].'.php')) {
  throw new Exception('Controller: '.$config['CONTROLLER_PATH'].$config['CONTROLLER'].'.php Not Found');
}

/* yes load it! */
require($config['CONTROLLER_PATH'].$config['CONTROLLER'].'.php');

/* is the class named properly? */
if (!class_exists($config['CONTROLLER'].'Controller')) {
	/* no throw an error */
  throw new Exception('Controller Class: '.$config['CONTROLLER_PATH'].$config['CONTROLLER'].' Not Found');
}

/* yes build the mvc class */
$classname = $config['CONTROLLER'].'Controller';
$GLOBALS['mvc'] = new $classname();
$GLOBALS['mvc']->data = &$data;

/* only "reserved" data variable */
$GLOBALS['mvc']->data['BASE_URL'] = $config['BASE_URL'];

/* put the config variables into $_SERVER to make them easier to read globally */
$_SERVER = array_merge($_SERVER,$config);

/* does the class method exist? */
if (method_exists($GLOBALS['mvc'],$config['FUNCTION'].'Action')) {
	/* yes call it and die */
	call_user_func_array(array($GLOBALS['mvc'],$config['FUNCTION'].'Action'),$config['SEGS']);
	die();
}

/* does the REST looking class exist? */
if (method_exists($GLOBALS['mvc'],$config['FUNCTION'].$config['METHOD'].'Action')) {
	/* if this is a PUT - jam the PUT into Global POST to make it easier to read */
	if ($config['METHOD'] == 'Put') parse_str(file_get_contents('php://input'), $_POST);
	/* yes call it and die */
	call_user_func_array(array($GLOBALS['mvc'],$config['FUNCTION'].$config['METHOD'].'Action', $config['SEGS']));
	die();
}

/* Not sure what else to do throw an error */
throw new Exception('Method: '.$config['CONTROLLER'].'-'.$config['FUNCTION'].'Action or '.$config['FUNCTION'].$config['METHOD'].'Action Not Found');

/* rewrite this function for your own custom 404 page */
function error_handler($exception) {
	global $config;
	header('HTTP/1.0 404 Not Found');
	extract(array('error'=>$exception->getMessage()));
	require($config['VIEW_PATH'].'404.php');
	die();
}

/* basic redirect */
function redirect($url='') {
  header('Location: '.$_SERVER['BASE_URL'].trim($url,'/'));
	header('Connection: close');
	die();
}

function mvc() {
	return $GLOBALS['mvc'];
}

/* Base Class */
class Controller {
	public $data = array();
	
	public function __set($name,$value) {
		$this->data[$name] = $value;
		return $this;
	}
	
	public function __get($name) {
		return $this->data[$name];	
	}
	
	/* set & get view data */
	public function data($name='',$val='#$#$') {
		if ($val == '#$#$') {
			return $this->data[$name];
		} else {
			$this->data[$name] = $val;
			return $this;
		}
	}
	
	/* load view into variable or output */
	/* the parameters need to be unique to not run into the extracted variables */
	public function view($mvc_viewfile='layout',$mvc_viewvariable='#$#$',$mvc_direct_output=false) {
		if ($mvc_viewvariable != '#$#$') {
		  extract($this->data);
		  ob_start();
		  require($_SERVER['VIEW_PATH'].$mvc_viewfile.'.php');
		  $this->data[$mvc_viewvariable] = ob_get_clean();
		  if ($mvc_direct_output === false) {
				return $this;
		  } else {
		  	$mvc_viewfile = ($mvc_direct_output === true) ? 'layout' : $mvc_direct_output ;
		  }
		}
	
	  extract($this->data);
	  require($_SERVER['VIEW_PATH'].$mvc_viewfile.'.php');
	}
	
} /* end mvc controller class */
