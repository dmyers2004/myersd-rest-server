<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class m_user extends crud {

  public function __construct() {
    parent::__construct('user','id,name,password,email,access_id,active,last_server_visit,last_gui_visit,tempkey,temppassword','id','created','modified');
  }

  public function login($email,$password) {
    $isgood = false;
    $this->CI->db->select('*, user.name uname');
    $this->CI->db->from($this->tablename);
    $this->CI->db->join('access', 'user.access_id = access.id');
    $this->CI->db->where(array('email'=>$email,'password'=>md5($password.$this->CI->config->item('encryption_key')),'active'=>1,'gui_access'=>1));
    $this->cursor = $this->CI->db->get()->result();
    if (count($this->cursor) === 1) {
      $this->record = $this->cursor[0];
      $this->CI->db->update($this->tablename,array('last_gui_visit'=>date('Y-m-d H:i:s')),array('id' => $this->record->id));
      $isgood = true;
    }
    return $isgood;
  }

  public function get_list() {
    $this->CI->db->select('*, user.id uid, user.created ucreated, user.name uname');
    $this->CI->db->join('access', 'user.access_id = access.id');
    $this->CI->db->order_by('user.name asc');
    $this->cursor = $this->CI->db->get($this->tablename)->result();
    $this->record = $this->cursor[0];
    return $this;
  }

} /* end class */
