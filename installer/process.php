<?php 
/*
Don Myers 2012
Open Software License v. 3.0 (OSL-3.0)
http://www.opensource.org/licenses/osl-3.0.php
*/
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>MyRESTful Server</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <a class="brand">MyRESTful Server Installer</a>
        <ul class="nav">
        </ul>
    </div>
  </div>
</div>
<div class="container">
<br><br><br><pre>
<?php
$n = chr(10);

ini_set('display_errors','On');
//error_reporting(E_ALL ^ E_NOTICE);

define('running',true);

error_reporting(E_ALL);

extract($_POST);

// create a server hash
$server_hash = md5(uniqid());
echo 'Created Server Hash'.$n; 

// hash the password
$password = md5($password.$server_hash);
echo 'Hashed Server Password'.$n;

// connection to db
$link = @mysql_connect($host,$dbname,$dbpassword) or die('could not connect');
@mysql_select_db($database) or die('could not select database');
echo 'Connected to DB'.$n;

// then load sql template
$sql = file_get_contents('templates/template.sql');
echo 'Loaded SQL Template'.$n;

$merge = array(
  'hash' => $server_hash,
  'now' => date('Y-m-d H:i:s'),
  'name' => $name,
  'password' => $password,
  'email' => $email,
  'host' => $host,
  'dbname' => $dbname,
  'dbpassword' => $dbpassword,
  'database' => $database
);
echo 'Merge Built'.$n;

// find and replace the fields in the sql template
merge($sql,$merge);
echo 'SQL Template Built'.$n;

// split into commands
$sqls = explode(';',$sql);
echo 'SQL Split'.$n;

// run the sql
foreach ($sqls as $s)
  safe_query($s);
echo 'SQL Run'.$n;

// create the db connection string
$db = $host.'/'.$dbname.'/'.$dbpassword.'/'.$database;
echo 'Connection String Built'.$n;

// empty server config folder
unlink('../server/config/config.php');
echo 'Deleted Server Config Folder'.$n;  

// create the server config.php (config.php) file
/*
$n = chr(10);	
$text = '<?php'.$n;
$sql = "select * from settings";
$dbc = safe_query($sql);
while ($dbr = mysql_fetch_array($dbc)) {
  if (is_numeric($dbr->value)) {
    $text .= '$config[\''.$dbr->slug.'\'] = '.$dbr->value.';'.$n;
  } else {
    $text .= '$config[\''.$dbr->slug.'\'] = \''.addcslashes($dbr->value,"'").'\';'.$n;
  }
}   
file_put_contents('../server/config/config.php',$text);
echo 'Server Config Built';
*/

// move the previous database.php file
rename('../'.$guifolder.'/application/config/database.php','../'.$guifolder.'/application/config/database-old.php');
echo 'Old Database Config Moved'.$n;

// load the ci database config template
$database_config = file_get_contents('templates/database.php');
echo 'Database Config Loaded'.$n;

// find and replace the fields
merge($database_config,$merge);
echo 'Database Config Setup'.$n;

// save
file_put_contents('../'.$guifolder.'/application/config/database.php',$database_config);
echo 'Database Saved'.$n;

// load the ci config file
$basic_config = file_get_contents('../'.$guifolder.'/application/config/config.php');
echo 'Config Loaded'.$n;

// find and replace the fields
$x = between("\$config['encryption_key']",";",$basic_config);

$basic_config = str_replace("\$config['encryption_key']".$x.";","\$config['encryption_key'] = '".$merge['hash']."';",$basic_config);
echo 'Config Setup'.$n;

// save
file_put_contents('../'.$guifolder.'/application/config/config.php',$basic_config);
echo 'Config Saved'.$n;

// add the first time file in the cache folder (it's r/w)
file_put_contents('../'.$guifolder.'/application/cache/firsttime','x');
echo 'Writing First Open File'.$n;

echo 'Finished'.$n;
echo 'Remember to REMOVE the install folder!'.$n;
echo 'And you must log into the Admin GUI once to finish the install'.$n;
?>
</pre>
</body>
</html>
<?php

/* functions */
function safe_query($query) {
  $query = trim($query);
  if (empty($query)) return FALSE;
  $result = @mysql_query($query);
  if (mysql_errno() > 0) die('Query Failed errorno='.mysql_errno().chr(10).'error='.mysql_error().chr(10).'query='.$query.chr(10));
  return $result;
}

function after($tag,$searchthis) {
	if (!is_bool(strpos($searchthis,$tag)))
	return substr($searchthis,strpos($searchthis,$tag)+strlen($tag));
}

function before($tag,$searchthis) {
	return substr($searchthis,0,strpos($searchthis, $tag));
}

function between($tag,$that,$searchthis) {
	return before($that,after($tag,$searchthis));
}

function merge(&$input,$data) {
  foreach ($data as $key => $value)
    $input = str_replace('{'.$key.'}',$value,$input);  
}