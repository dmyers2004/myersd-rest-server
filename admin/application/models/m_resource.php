<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class m_resource extends crud {

  public function __construct() {
    parent::__construct('resource','id,object,method,old,type,version,dbconnection','id','created','modified');
  }

  public function read2array() {
    $ary = array();

    $this->sql("select * from ".$this->tablename." where method = 'get' or method = 'post' or method = 'put' or method = 'delete' order by object, method");
    foreach ($this->cursor as $obj) {
      $ary[$obj->object][] = $obj;
    }

    $this->sql("select * from ".$this->tablename." where method <> 'get' and method <> 'post' and method <> 'put' and method <> 'delete' order by object, method");
    foreach ($this->cursor as $obj) {
      $ary[$obj->object][] = $obj;
    }

    return $ary;
  }

  public function update_table() {
    $models = $this->parse_files();

    /* we need to safely delete the old */
    $this->sql("update ".$this->tablename." set old = 0");

    foreach ($models as $obj => $methods) {
      foreach ($methods['models'] as $method) {
				$this->upsert($obj,$method,$methods['type'],$methods['version'],$methods['dbconnection']);
      }
    }

    $this->CI->load->model('m_access_resource');
    /* now find all of the 0 (not valid anymore) and remove them from the join table */
    $query = $this->sql("select * from ".$this->tablename." where old = 0")->cursor;
    foreach ($query as $rec) {
      $this->CI->m_access_resource->delete_by_resource_id($rec->id);
      $this->sql("select * from ".$this->tablename." where id = ".$rec->id);
    }

    /* and remove them from the resource table */

    $this->sql("delete from ".$this->tablename." where old = 0");
  }

  public function upsert($obj,$method,$type,$version,$dbconnection) {
    $id = $this->get_special($obj,$method);
    $data = array('object'=>$obj,'method'=>$method,'type'=>$type,'version'=>$version,'old'=>1,'dbconnection'=>$dbconnection);
    if ($id == 0) {
      $data['created'] = date('Y-m-d H:i:s');
      $this->insert($data);
    } else {
      $this->update($id,$data);
    }
  }

  public function get_special($obj,$method) {
    $this->sql("select * from ".$this->tablename." where object = ".$this->CI->db->escape($obj)." and method = ".$this->CI->db->escape($method));
    if (count($this->cursor) > 0) {
      return $this->record->id;
    } else {
      return 0;
    }
  }

	private function parse_files() {
    $models = array();

    /* define this or the models won't load */
    define('RUNNING',true);

    $libraries_folder = $this->CI->m_settings->cache['server_folder'].'libraries';
    require_once($libraries_folder.'/model_root.php');
    require_once($libraries_folder.'/model_db_base.php');
    require_once($libraries_folder.'/model_file_base.php');
    require_once($libraries_folder.'/model_function_base.php');

    $models_folder = $this->CI->m_settings->cache['server_folder'].'models';
    $files = glob($models_folder.'/*.php');
    foreach ($files as $file) {
      require_once($file);
      $model_name = 'model_'.basename($file,'.php');
			$type = get_parent_class($model_name);
			$version = $this->get_version($file);
			$db_connection = ($type == 'model_db_base') ? $this->get_db_connection($file) : '';
      $list = get_class_methods($model_name);
      if (is_array($list)) {
        $newlist = array();
        foreach ($list as $key => $value) {
          $short_name = substr($value,5);
          $prefix = substr($value,0,5);
          if ($prefix == 'func_' || $prefix == 'rest_') $newlist[$value] = $short_name;
        }
        if (count($newlist)>0) $models[$model_name] = array('models'=>$newlist,'type'=>$type,'version'=>$version,'dbconnection'=>$db_connection);
      }
    }
    return $models;
  }

  private function get_version($file) {
    /* public $version = '1.0'; */
    $a = explode('$version',file_get_contents($file));
    $b = explode(';',$a[1]);
    return trim($b[0],'\'" =');
  }

  private function get_db_connection($file) {
    /*   public $db_connection = 'server_a'; */
    $a = explode('$connection',file_get_contents($file));
    $b = explode(';',$a[1]);
    $c = trim($b[0],'\'" =');
    $dbconnection = (empty($c)) ? 'default_server_connection'  : $c;
    return $dbconnection;
  }

} /* end class */
