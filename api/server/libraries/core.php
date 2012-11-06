<?php if (RUNNING !== true) die();
/*
Don Myers 2012
Open Software License v. 3.0 (OSL-3.0)
http://www.opensource.org/licenses/osl-3.0.php
*/

/* get reference to core super class */
function &get_instance() {
  global $core;
  return $core;
}

/* core class */
class core {
  public $main_db_connection; /* main database connection */
  public $userdata = array(); /* user data storage */
  public $data = array(); /* output array */

  public function __construct() {
    /* some defaults */
    $this->time_start = microtime(true);
    $this->today = date('Y-m-d');
    $this->root_url = (isset($_SERVER["HTTPS"])) ? 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    /* load the config file */
    if (!file_exists('server/config/config.php')) die('config file not found please run admin gui first');
    require('server/config/config.php');
    $this->config = $config;

    /* are the cache & log dirs writable? */
    if (!is_dir('server/cache') || !is_writable('server/cache')) die('cache folder not accessible');
    if (!is_dir('server/logs') || !is_writable('server/logs')) die('logs folder not accessible');
				
    /* connect the main database */
    $this->main_db_connection = $this->connect('server_connection');

    /* load the base models */
    require('server/libraries/model_root.php');
    require('server/libraries/model_db_base.php');
    require('server/libraries/model_file_base.php');
    require('server/libraries/model_function_base.php');
    
    /* check login or die */
    $this->login_auth();

		$ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
		if (!$ajax && $this->config['ajax_only']) {
      $this->methodnotallowed(); /* show error and die */
		}
		
    /* let's get started */
    $this->method = strtolower($_SERVER['REQUEST_METHOD']);
    
    if (!in_array($this->method,array('get','post','delete','put','options','head'))) {
      $this->methodnotallowed(); /* show error and die */
    }

    /* jam the PUT & POST stuff in $this->request */
    if ($this->method == 'put') {
      parse_str(file_get_contents('php://input'), $this->request);
    }

    $this->request = ($this->method == 'post') ? $_POST : $this->request;
    $this->segs = explode('/',trim(urldecode($this->clean($_SERVER["REQUEST_URI"])),'/'));
		
    /* shift everything off the uri before the model */
    for ($p = 2;$p <= $this->config['model_segment'];$p++) {
      array_shift($this->segs);
    }

    /* if they didn't include a model use the default */
    $this->model_name = (!empty($this->segs[0])) ? $this->segs[0] : $this->config['default_model'];

    /* test access this only covers the basic get/post/put/delete - additional check are needed in your models to check individual functions */
    if ($this->check($this->model_name,$this->method) !== true) {
      $this->notacceptable('Access Denied:'.$this->model_name.':'.$this->method);
    }

    /* shift off the model name */
    array_shift($this->segs);

    /* basic setup done - return so a valid $core object exists */
  }

  public function run() {
    /* now let's find the model and try to run the method */

    /* does the model exist */
    if (!file_exists('server/models/'.$this->model_name.'.php')) {
      $this->notacceptable('model '.$this->model_name.' not found');
    }

    /* yes load it */
    require('server/models/'.$this->model_name.'.php');

    /* does the required model class now exists? */
    if (!class_exists('model_'.$this->model_name)) {
      $this->notacceptable('model object '.$this->model_name.' not found');
    }

    /* create the class */
    $class_name = 'model_'.$this->model_name;
    $this->model = new $class_name();

    /* setup the method name */
    $method_name = 'rest_'.$this->method;

    /* does the method exist? */
    if (!method_exists($this->model,$method_name)) {
      $this->notacceptable('method not found '.$method_name);
    }

    /* call it */
    call_user_func(array($this->model,$method_name));

    return $this;
  }

  public function advget($ary,$name,$default=null,$clean=false) {
    if (isset($ary[$name])) {
      if ($clean) {
        return $this->clean($ary[$name]);
      } else {
        return $ary[$name];
      }
    }
    return $default;
  }

  public function request($name,$default=null,$clean=false) {
    return $this->advget($this->request,$name,$default,$clean);
  }

  public function seg($num,$default=null,$clean=false) {
    return $this->advget($this->segs,$num,$default,$clean);
  }

  public function segc() {
    return count($this->segs);
  }

  public function clean($input) {
    return preg_replace("/[^a-zA-Z\s0-9,\_\-\/\.]*/",'',$input);
  }

  /* gui log function */
  public function log() {
    if (!$this->config['server_access_log']) return;
    $data = array(
      'type'=>2, /* server entry */
      'time'=>date('Y-m-d H:i:s'),
      'object'=>@$this->model_name,
      'method'=>@$this->method,
      'request'=>serialize($this->request),
      'agent'=>trim(@$_SERVER['HTTP_USER_AGENT']),
      'ip'=>$_SERVER['REMOTE_ADDR'],
      'url'=>$_SERVER['REQUEST_URI'],
      'memory'=>memory_get_usage(true),
      'pmemory'=>memory_get_peak_usage(true),
      'etime'=>microtime(true) - $this->time_start
    );
    $data['args'] = @implode('/',$this->segs);
    $data['auth_user'] = trim(@$_SERVER['PHP_AUTH_USER']);
    $data['user_id'] = (!isset($this->userdata['name'])) ? $this->config['no_user'] : $this->userdata['name'];

    file_put_contents('server/logs/log-'.$this->today.md5('log-'.$this->today.$this->config['key']),serialize($data).chr(10),FILE_APPEND | LOCK_EX);
  }

  /* database functions - these may need to be rewritten for your db type */
  public function query($query,$link) {
    if (empty($query)) return false;
    $data = @mysql_query($query,$link);
    if (mysql_errno($link) > 0) {
      if (!$this->config['show_sql_errors']) {
        $this->badrequest(mysql_errno($link),'MySQL General Fault'); /* error and die */
      } else {
        $this->badrequest(mysql_errno($link),mysql_error($link).':'.$query); /* error and die - dev version */
      }
    }
    return $data;
  }

  public function connect($name) {
    $parts = @explode('/',@$this->config[$name]);
    /* because the access/gui database could be the same host, username and password always open a new connection - php will try to cache if not */
    $link = mysql_connect($parts[0],$parts[1],$parts[2],true) or $this->badrequest(1,'could not connect to database '.$parts[0]);
    mysql_select_db($parts[3],$link) or $this->badrequest(1,'could not select database '.$parts[3]);
    return $link;
  }

  /* cache functions */
  public function read_cache($key) {
    $key = 'server/cache/'.md5($key.$this->config['key']);

    if (!file_exists($key) || (filemtime($key) < (time() - $this->config['cache_expiration']))) {
      return null;
    }
    
    if (filesize($key) == 0) {
      return null;
    }

    return(unserialize(file_get_contents($key)));
  }

  public function write_cache($key, $data) {
    $folder = 'server/cache';
    $file = tempnam($folder,'temp-');
    file_put_contents($file,serialize($data));
    rename($file,$folder.'/'.md5($key.$this->config['key']));
  }

  /* output responds - we are only dumping json out */

  public function output($input=false) {
    if ($input) {
      header('Content-type: application/json');
      echo json_encode($this->data);
    }
    $this->log();
    die();
  }

  /* Send a HTTP 200 response header. */
  public function ok() {
  	header('HTTP/1.0 200 OK');		  
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    $this->output(true);
  }

  /* Send a HTTP 201 response header. */
  public function created($url = false) {
    header('HTTP/1.0 201 Created');
    if ($url) header('Location: '.$url);
    $this->output(true);
  }

  /* Send a HTTP 204 response header. */
  public function nocontent() {
    header('HTTP/1.0 204 No Content');
    $this->output();
  }

  /* Send a HTTP 400 response header. */
  public function badrequest($errno=0,$errtxt='error') {
    header('HTTP/1.0 400 Bad Request');
    $this->data = array();
    $this->data['errno'] = $errno;
    $this->data['errtxt'] = $errtxt;
    $this->output(true);
  }

  /* Send a HTTP 401 response header. */
  public function unauthorized($realm = 'Private Realm') {
    header('WWW-Authenticate: Basic realm="'.$realm.'"');
    header('HTTP/1.0 401 Unauthorized');
    $this->output();
  }

  /* Send a HTTP 404 response header. */
  public function notfound() {
    header('HTTP/1.0 404 Not Found');
    $this->output();
  }

  /* Send a HTTP 405 response header. */
  public function methodnotallowed($allowed = 'GET, POST, PUT, DELETE') {
    header('HTTP/1.0 405 Method Not Allowed');
    header('Allow: '.$allowed);
    $this->output();
  }

  /* Send a HTTP 406 response header. */
  public function notacceptable($input='') {
    header('HTTP/1.0 406 Not Acceptable');
    $this->data['error'] = $input;
    $this->output(true);
  }

  /* Send a HTTP 500 response header. */
  public function internalservererror() {
    header('HTTP/1.0 500 Internal Server Error');
    $this->output();
  }

  /* access handler */
  public function login_auth() {
    if ($this->config['server_auth'] == 0) return true;

    /* setup invalid inputs */
    $username = null;
    $password = null;

    /* did they send in anything? */
    if (isset($_SERVER['PHP_AUTH_USER'])) {
      $username = $_SERVER['PHP_AUTH_USER'];
      $password = $_SERVER['PHP_AUTH_PW'];
    }

    /* if they are still null then they didn't send anything in */
    if (is_null($username)) $this->unauthorized($this->config['realm']);

    /* test what they sent in - this isn't cached */
    $user_id = $this->login($username,$password);
    if ($user_id === false) {
      $this->notfound(); /* if not true show 404 and die */
    }

    /* cache everything so we don't need to search around the db to find access and such */
    $this->userdata = $this->read_cache('userobject'.$user_id);
    if ($this->userdata === null) {
      $sql = "select resource.id as resource_id, user.name as name, user.email as email, object, method, access.name as access_name, access.id as access_id, user.id as user_id from user left join access on access.id = user.access_id left join access_resource on access_resource.access_id = access.id left join resource on resource.id = access_resource.resource_id where user.id = '".mysql_real_escape_string($user_id)."'";
      $dbc2 = $this->query($sql,$this->main_db_connection);
      while($dbr2 = mysql_fetch_object($dbc2)) {
        $this->userdata[$dbr2->object][$dbr2->method] = true;
        /* these are the same for all records so we will just keep settings them */
        $this->userdata['name'] = $dbr2->name;
        $this->userdata['email'] = $dbr2->email;
        $this->userdata['access'] = $dbr2->access_name;
        $this->userdata['user_id'] = $dbr2->user_id;
        $this->userdata['access_id'] = $dbr2->access_id;
      }
      $this->write_cache('userobject'.$user_id,$this->userdata);
    }

    /*
    Cache the server visits in a cache file and only load on gui dashboard load This should be faster then writing to the DB every call
    This is only used to determine when a user last visited the server
    */
    if ($this->config['server_auth_log']) {
      file_put_contents('server/logs/access-log-'.$this->today.md5('access-log-'.$this->today.$this->config['key']),serialize(array('id'=>$user_id,'ts'=>date('Y-m-d H:i:s'))).chr(10),FILE_APPEND | LOCK_EX);
    }

    return true;
  }

  /* DRY login - incase it's needed else where */
  public function login($username,$password) {
    $sql = "select `id` from `user` where `email` = '".mysql_real_escape_string($username)."' and `password` = '".mysql_real_escape_string(md5($password.$this->config['key']))."' and active = 1";
		//die($sql);
    $dbc = $this->query($sql,$this->main_db_connection);
    /* did we get exactly 1? if not we didn't get any or something else went wrong */
    if (mysql_num_rows($dbc) === 1) {
      return @mysql_result($dbc,0);
    }
    return false;
  }

  /* DRY check - incase it's needed in a model */
  public function check($o,$m) {
    if ($this->config['server_auth'] == 0) return true;
    return isset($this->userdata['model_'.$o][$m]);
  }

} /* close core class */