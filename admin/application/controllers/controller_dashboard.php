<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class controller_dashboard extends MY_Admin_controller {

  public function __construct() {
    parent::__construct();
    /* do some house keeping */
    $this->load->helper('view');
    $this->load->library('maintenance');
    $this->maintenance->cleanup();
  }

  public function index($user='') {
    $this->gui($user);
  }

  public function gui($user='') {
    $this->_display(1,$user);
  }

  public function server($user='') {
    $this->_display(2,$user);
  }

  /* view */
  private function _display($type,$user) {
    $this->data['type'] = $type;
    $where = array('type'=>$type);
    if ($user != '') {
      $this->data['filter'] = $where['user_id'] = urldecode($user);
    }
    $this->data['page'] = 'server';
    $this->data['ptitle'] = 'Server';
    if ($type == 1) {
      $this->data['page'] = 'gui';
      $this->data['ptitle'] = 'GUI';
    }
    $this->data['log'] = $this->m_gui_log->select(null,$where,'time desc',$this->m_settings->cache['log_entries'])->cursor;
    $this->session->set_userdata('logredirect',uri_string());
    $this->render('dashboard/index');
  }
  
  /* view */
  public function details($id=0) {
    $this->data['rtn'] = $this->session->userdata('logredirect');
    if ($id == 0) redirect($this->data['rtn']);
    $this->data['entry'] = $this->m_gui_log->select($id)->record;
    $this->render();
  }
  
  /* ajax */
  public function delete($which=null,$num=0) {
		if ($this->input->is_ajax_request()) {
			$which = urldecode($which);
			$this->m_gui_log->delete(array('type',(int)$num))->gui_entry('dashboard','delete',$which);
      $this->flash_msg->green($which.' Log Emptied');
		}
  }
    
} /* end class */
