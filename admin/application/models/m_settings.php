<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class m_settings extends crud {
	public $cache;

  public function __construct() {
    parent::__construct('settings','id,slug,value,usage,usedon,canedit,root','id','created','modified');
		$this->cache();
  }
	
	public function cache() {
		/* let's get them and cache them! */
    $this->select(null,null,'slug asc');
		foreach ($this->cursor as $rec) {
			$this->cache[$rec->slug] = $rec->value;
		}
	}
	
	public function get_usedon_options() {
	  return array(1=>'REST Server',2=>'Admin GUI',3=>'Both');
	}
	
	public function delete_unroot($id) {
    $this->CI->db->where('root',0);
    $this->CI->db->where($this->primary,$id);
    $this->CI->db->delete($this->tablename);
    return $this->CI->db->affected_rows();
	}
	
} /* end class */
