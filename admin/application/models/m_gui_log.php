<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class m_gui_log extends crud {

  public function __construct() {
    // Call the Model constructor
    parent::__construct('gui_log','id,type,time,user_id,object,method,args,request,agent,ip,url,url,memory,pmemory','id');
    $this->CI->load->library('user_agent');
  }

  public function gui_entry($object,$method,$args='') {
		$user = ($this->CI->session->userdata('name')) ? $this->CI->session->userdata('name') : $this->CI->m_settings->cache['no_user'];
    
    if (isset($_REQUEST['password'])) {
      $_REQUEST['password'] = str_pad('', strlen($_REQUEST['password']),'x');
    }
    
    $data = array(
      'type'=>1,
      'time'=>date('Y-m-d H:i:s'),
      'user_id'=>$user,
      'object'=>$object,
      'method'=>$method,
      'args'=>$args,
      'request'=>serialize($_REQUEST),
      'agent'=>$this->CI->agent->agent_string(),
      'ip'=>$this->CI->input->ip_address(),
      'url'=>$_SERVER['REQUEST_URI'],
      'memory'=>memory_get_usage(),
      'pmemory'=>memory_get_peak_usage()
    );

    $this->CI->db->insert($this->tablename,$data);
  }
  
  public function server_entry($data) {
    if (is_array($data)) {
      $this->CI->db->insert($this->tablename,$data);
    }
  }
  
}
