<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class controller_access extends MY_Admin_controller {

  public function __construct() {
    parent::__construct();

    $this->load->model('m_access');
    $this->load->model('m_access_resource');
    $this->load->model('m_resource');
  }

  /* view */
  public function index() {
    $this->load->helper('view');
    $this->data['access'] = $this->m_access->select(null,null,'name asc')->cursor;
    $this->render();
  }

  /* edit */
  public function edit($id=null) {
    $this->load->helper('form');
    $this->load->helper('view');
    
		$this->session->set_userdata('redirectto', $_SERVER["HTTP_REFERER"]);
    
		if ($id == null) {
      $this->data['mode'] = 'New';
      $this->data['access'] = new stdClass();
    } else {
      $this->data['mode'] = 'Edit';      
      $this->data['access'] = $this->m_access->select($id)->record;
    }

    $this->data['resource'] = $this->m_resource->read2array();
    $this->data['access_resource'] = $this->m_access_resource->select(null,array('access_id'=>$id))->cursor;
    $this->render();
  }

  /* redirect */
  public function upsert($id=null) {
    if ($this->input->post('btn') == 'upsert') {

      // update access (group)
      $this->m_access->id = $id;
      $this->m_access->copy()->upsert();

      // first dump all this access's current records
      $this->m_access_resource->delete_by_access_id($this->m_access->insertid);

      $access_ary = $this->input->post('access',null);
      if (is_array($access_ary)) {
        foreach ($access_ary as $resource_id) {
          $data = array('access_id'=>$this->m_access->insertid,'resource_id'=>$resource_id);
          $this->m_access_resource->insert($data);
        }
      }
      $this->m_gui_log->gui_entry('access','upsert',$id);
      $this->flash_msg->green('Access Saved');
    }
    redirect($this->session->userdata('redirectto'));
  }

  /* ajax */
  public function delete($id=null) {
		if ($this->input->is_ajax_request()) {
			$this->m_access->delete((int)$id);
			$this->m_access_resource->delete_by_access_id($id);
			$this->m_gui_log->gui_entry('access','delete',$id);
      $this->flash_msg->green('Access Deleted');
		}
  }

} /* end class */
