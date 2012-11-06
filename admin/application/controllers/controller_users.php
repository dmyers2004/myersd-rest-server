<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class controller_users extends MY_Admin_controller {

  public function __construct() {
    parent::__construct();
		
    $this->load->helper('view');
    $this->load->model('m_user');
  }
  
  /* view */
  public function index() {
    $this->load->model('m_access');
    $this->data['access_options'] = $this->m_access->select('id,name')->cursor2list();
    $this->data['users'] = $this->m_user->get_list()->cursor;
    $this->render();
  }

  /* view */
  public function edit($id=null) {
    $this->load->helper('form');
    $this->load->model('m_access');

    if ($id == null) {
      $this->data['mode'] = 'New';
      $this->data['user'] = new stdClass();
    } else {
      $this->data['user'] = $this->m_user->select($id)->record;
      $this->data['user']->password = '';
      $this->data['mode'] = 'Edit';
    }

    $this->data['access_options'] = $this->m_access->select('id,name')->cursor2list();
    $this->render();
  }

  /* redirect */
  public function upsert($id=null) {
    if ($this->input->post('btn') == 'upsert') {
      $this->m_user->id = $id;
      $this->m_user->active = $this->input->post('active',0);

      $this->m_user->copy();

      if ($this->input->post('password','') != '') {
        $this->m_user->password = md5($this->input->post('password').$this->config->item('encryption_key'));
      }

      $this->m_user->upsert();

      $this->m_gui_log->gui_entry('users','upsert',$id);
      $this->flash_msg->green('User Saved');
    }
    
    redirect('/users');
  }

  /* ajax */
  public function delete($id=null) {
		if ($this->input->is_ajax_request()) {
			$this->m_user->delete((int)$id);
			$this->m_gui_log->gui_entry('users','delete',$id);
      $this->flash_msg->green('User Deleted');
		}
  }

} /* end class */
