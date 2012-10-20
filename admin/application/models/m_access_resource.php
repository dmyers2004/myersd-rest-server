<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class m_access_resource extends crud {

  public function __construct() {
    parent::__construct('access_resource','access_id,resource_id');
  }

  public function delete_by_access_id($access_id) {
    $this->CI->db->delete($this->tablename,array('access_id'=>$access_id));
  }

  public function delete_by_resource_id($resource_id) {
    $this->CI->db->delete($this->tablename,array('resource_id'=>$resource_id));
  }

  public function delete($access_id,$resource_id) {
    $this->CI->db->delete($this->tablename,array('access_id'=>$access_id,'resource_id'=>$resource_id));
  }

} /* end class */
