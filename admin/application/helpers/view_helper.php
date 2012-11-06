<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function mysql_timestamp_format($format = 'F j, Y, g:i:s a',$mytimestamp) {
  $ts = (int)strtotime($mytimestamp);
  if ($ts > 10) $rtn = date($format,$ts);
  return @$rtn;
}

function resource($id,$access_resource) {
  if (!is_array($access_resource)) return false;
  foreach ($access_resource as $x) {
    if ($x->resource_id == $id) return true;
  }
  return false;
}

function gui_log_type($type) {
  $output = '';
  switch ($type) {
    case 1:
      $output = '<i class="icon-user"></i> GUI';
    break;
    case 2:
      $output = '<i class="icon-fire"></i> Server';    
    break;
  }
  return $output;
}

function highlight_defaults($method) {
  $class = 'badge-error';
  $defaults = array('get','put','post','delete');
  if (in_array($method,$defaults)) $class = 'badge-info';
  return '<div class="blob badge '.$class.'">'.$method.'</div>';
}

function pad($input,$len) {
  return '<div style="width: '.$len.'%; float: left; overflow: hidden; height: 18px">'.$input.'</div>';
}

function formatBytes($bytes, $precision = 2) { 
    $units = array('b', 'kb', 'mb', 'gb', 'tb'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= pow(1024, $pow);

    return round($bytes, $precision).$units[$pow]; 
}

function getmodeltype($input) {
	$split = explode('_',$input);
	return $split[1];
}

function tooltip($input,$title='') {
	$output = '';
	if (!empty($input)) {
		$output = ' rel="popover" data-content="'.str_replace('"','&quot;',$input).'" data-title="'.$title.'" ';
	}
	return $output;
}

function usedon_icon($id) {
  if ($id == 1) return '<i class="icon-fire"></i>';
  if ($id == 2) return '<i class="icon-user"></i>';
  if ($id == 3) return '<i class="icon-fire"></i><i class="icon-user"></i>';
  return '';
}

function hidepass($input,$slug) {
  $ary = explode('/',$input);
  if ($slug == 'default_server_connection') {
    $ary[2] = str_pad('', strlen($ary[2]),'*');
    $input = implode('/',$ary);
  }
  return $input;
}