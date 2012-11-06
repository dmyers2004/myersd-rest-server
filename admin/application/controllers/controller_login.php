<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class controller_login extends MY_Controller {

  /* view */
  public function index() {
    $this->load->helper('form');
    $this->render();
  }

  /* redirect */
  public function logout() {
    $this->m_gui_log->gui_entry('login','logout');
    $this->session->sess_destroy();
    redirect('/');
  }

  /* redirect */
  public function login() {
    $this->load->model('m_user');
    $pass = $this->m_user->login($this->input->post('email'),$this->input->post('password'));
    
    if ($pass === true) {
      $this->session->set_userdata(md5($this->m_settings->cache['realm']),true);
      $this->session->set_userdata('id',$this->m_user->record->id);
      $this->session->set_userdata('name',$this->m_user->record->uname);
      $this->session->set_userdata('email',$this->m_user->record->email);
      $this->session->set_userdata('access_id',$this->m_user->record->access_id);
      $this->m_gui_log->gui_entry('login','login');
      redirect('dashboard');
    } else {
      $this->m_gui_log->gui_entry('login','login','failed');
      $this->flash_msg->red('Your login has failed. Please type again');
      redirect('');
    }
  }

} /* end class */
