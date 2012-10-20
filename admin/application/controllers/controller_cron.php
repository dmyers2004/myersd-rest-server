<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class controller_cron extends MY_Controller {

  /* ajax */
  public function index() {
    if (!$this->input->is_ajax_request()) die();

    ignore_user_abort(TRUE);
		ini_set('max_execution_time',600);
		
    $this->load->library('maintenance');  
    $this->maintenance->user_access_logs();
    $this->maintenance->general_access();
    
    die();
  }

} /* end class */
