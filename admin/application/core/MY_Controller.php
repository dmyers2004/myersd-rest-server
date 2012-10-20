<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
  public $data = array();

  public function __construct() {
    parent::__construct();
		$this->router->class = substr($this->router->class,11);
	  $this->data['class'] = $this->router->class;
	}

  public function render($view = NULL,$layout='main') {
    $view = ($view == NULL) ? $this->router->class.'/'.$this->router->method : $view;
    $this->data['container'] = $this->load->view($view, $this->data, TRUE);
    $this->load->view('layouts/'.$layout, $this->data, FALSE);
  }

}

class MY_Admin_controller extends MY_Controller {
	public function __construct() {
		parent::__construct();
		if ($this->session->userdata(md5($this->m_settings->cache['realm'])) !== true) {
		  redirect('');
	  }
	}
}
