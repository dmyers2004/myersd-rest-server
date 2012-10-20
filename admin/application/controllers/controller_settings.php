<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class controller_settings extends MY_Admin_controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('m_settings');
    $this->data['usedon_options'] = $this->m_settings->get_usedon_options();
  }

  /* view */
  public function index() {
    $this->load->helper('view');
    $this->data['settings'] = $this->m_settings->select(null,array('canedit'=>1),'usedon desc, slug asc')->cursor;
    $this->render();
  }

  /* view */
	public function edit($id=null) {
    $this->load->helper('form');
    
    if ($id == null) {
      $this->data['mode'] = 'New';
      $this->data['rec'] = new stdClass();
    } else {
      $this->data['mode'] = 'Edit';
      $this->data['rec'] = $this->m_settings->select($id)->record;
    }

    $this->render();
  }

  /* redirect */
  public function upsert($id=null) {
    if ($this->input->post('btn') == 'upsert') {
      if ($id == null) {
        $this->m_settings->canedit = 1;
        $this->m_settings->root = 0;
      }
      $this->m_settings->id = $id;
      $this->m_settings->copy()->upsert();

			$this->m_gui_log->gui_entry('settings','upsert',$id);
      $this->flash_msg->green('Setting Saved');
    }

	  /* update the gui cache */
    $this->m_settings->cache();

    /* bump the server cache */
    $this->load->library('maintenance');
    $this->maintenance->updateserversettings();
    
    redirect('/settings');
  }

  /* ajax */
  public function delete($id=null) {
		if ($this->input->is_ajax_request()) {
			$rows = $this->m_settings->delete_unroot((int)$id);
			if ($rows > 0) {
  			$this->m_gui_log->gui_entry('settings','delete',$id);
        $this->flash_msg->green('Setting Deleted');
			} else {
        $this->flash_msg->red('You cannot delete root settings');
			}
		}
	}
	
} /* end class */
