<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Flash_msg {
  public $CI;
  public $messages = array();
  public $type = array(
    'red'=>'error',
    'blue'=>'info',
    'yellow'=>'block',
    'green'=>'success',
    'error'=>'error',
    'info'=>'info',
    'block'=>'block',
    'success'=>'success'
  );
  public $staytime = 3; /* setup the initial pause time seconds */

  public function __construct() {
    $this->CI =& get_instance();
    
    /* test dependancies */
    if (!isset($this->CI->asset) || !isset($this->CI->session)) show_error('The Asset &amp; Session Libraries are required to be loaded before the Flash Msg Library');
    
    /* attach header content */
    $this->CI->asset->link('flash_msg.css'); /* load the flash_msg css */
    $this->CI->asset->script('jquery.bootstrap.growl.js'); /* load the flash_msg js */
    $this->CI->asset->extra($this->html());
  }

  /* most basic add function */
  public function add($msg='',$type='yellow',$sticky=FALSE) {
  	$this->messages[] = array('msg'=>$msg,'type'=>$this->type[$type],'sticky'=>$sticky);
    $this->CI->session->set_flashdata('flash_messages',$this->messages);
  }
  
  /* wrapper functions for add */
  public function __call($method, $param) {
    if (array_key_exists($method,$this->type)) {
      $sticky_default = (($method == 'red' || $method == 'error') && !is_bool($param[1])) ? TRUE : $param[1];
      call_user_func_array(array($this,'add'), array($param[0],$method,$sticky_default));
    }
  }

  public function html() {
    $html = '';
    $msgs = $this->CI->session->flashdata('flash_messages');
    if (is_array($msgs)) {
    	$html = '<script>$(document).ready(function(){';
    	foreach ($msgs as $key => $msg) {
    	  $staytime = ($msg['sticky'] == TRUE) ? '' : ', stayTime: '.(1000 * $this->staytime++);
    		$html .= 'jQuery.noticeAdd({ text: \'<strong>'.ucwords($msg['type']).':</strong> '.$msg['msg'].'\', stay: \''.$msg['sticky'].'\', type: \''.$msg['type'].'\''.$staytime.' });';
    	}
    	$html .= '})</script>';
    }
    return $html;
  }

} /* end class */