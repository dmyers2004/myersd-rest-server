<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Crud {

  public $tablename;
  public $primary;
  public $cursor;
  public $record;
  public $insertid;
  public $created;
  public $modified;
  public $CI;
  public $data = array();
  public $fields = array();
  public $idx = 0;

  public function __construct($tablename=NULL,$fields=NULL,$primary=NULL,$created=NULL,$modified=NULL) {
    $this->CI =& get_instance();

    if ($tablename != NULL) $this->tablename = $tablename;
    $this->fields = explode(',',$fields);
    if ($primary != NULL) $this->primary = $primary;
    $this->created = $created;
    $this->modified = $modified;
  }

  public function sql($sql) {
    $cursor = $this->CI->db->query($sql);

    if (!is_bool($cursor)) {
      $this->cursor = $cursor->result();
      $this->record = $this->cursor[$this->idx];
    }

    return $this;
  }

  public function select($id=NULL,$where=NULL,$orderby=NULL,$limit=NULL,$offset=NULL) {
    if ($id != NULL && strpos($id,',') === FALSE) {
      $this->CI->db->where(array($this->primary=>$id));
    }

    if (strpos($id,',') !== FALSE) {
      $this->CI->db->select($id);
    }

    if ($where != NULL) {
      $this->CI->db->where($where);
    }

    if ($orderby != NULL) {
      $this->CI->db->order_by($orderby);
    }
  
    if ($limit != NULL && $offset != NULL) {
      $this->CI->db->limit($limit,$offset);
    }

    if ($limit != NULL && $offset == NULL) {
      $this->CI->db->limit($limit);
    }

    $this->cursor = $this->CI->db->get($this->tablename)->result();
    $this->record = $this->cursor[$this->idx];
    return $this;
  }
  
  public function cursor2array() {
    $ary = array();
    foreach ($this->cursor as $idx => $obj) {
      $obj_ary = (array)$obj;
      $ary[$obj_ary[$this->primary]] = $obj_ary;
    }
    return $ary;
  }
  
  public function record2array() {
    return (array)$this->record;
  }
  
  public function cursor2list() {
    $ary = array();
    foreach ($this->cursor as $obj) {
      $values = array_values((array)$obj);
      $ary[$values[0]] = $values[1];
    }
    return $ary;  
  }
  
  public function insert($data=NULL) {
    if ($data != NULL) $this->data= $data;
    if ($created != NULL) $this->data[$this->created] = date('Y-m-d H:i:s');
    if ($modified != NULL) $this->data[$this->modified] = date('Y-m-d H:i:s');
    $this->CI->db->insert($this->tablename,$this->data);
    $this->insertid = $this->CI->db->insert_id();
    $this->affected_rows = $this->CI->db->affected_rows();
    return $this;
  }

  public function update($id=NULL,$data=NULL) {
    if ($data != NULL) $this->data=$data;
    if ($id != NULL) $this->data[$this->primary] = $id;
    if ($modified != NULL) $this->data[$this->modified] = date('Y-m-d H:i:s');
    $this->CI->db->update($this->tablename, $this->data, array($this->primary => $this->data[$this->primary]));
    $this->insertid = $this->data[$this->primary];
    $this->affected_rows = $this->CI->db->affected_rows();
    return $this;
  }

  public function upsert($id=NULL,$data=NULL) {
    if ($data != NULL) $this->data=$data;
    if ($id != NULL) $this->data[$this->primary] = $id;
    if ($this->data[$this->primary] == 0 || $this->data[$this->primary] == NULL) return $this->insert();
    else return $this->update($this->data[$this->primary]);
  }

  public function delete($id=NULL) {
    if ($data != NULL) $this->data=$data;
    if (is_array($id)) $this->CI->db->where($id[0],$id[1]);
    else $this->CI->db->where($this->primary,$this->data[$this->primary]);
    $this->CI->db->delete($this->tablename);
    $this->affected_rows = $this->CI->db->affected_rows();
    return $this;
  }

  public function copy() {
    foreach ($this->CI->input->post() as $key => $value) {
      if (in_array($key,$this->fields)) {
        $this->data[$key] = $this->CI->input->post($key);
      }
    }   
    return $this;
  }
  
  public function __set($name, $value) {
    if (in_array($name,$this->fields)) {
      $this->data[$name] = $value;
    }
  }
  
  public function __get($name) {
    if (in_array($name,$this->fields)) {
      return @$this->data[$name];
    }
  }
  
  public function next() {
    if (($this->idx + 1) >= count($this->cursor)) return FALSE;
    $this->idx++;
    $this->record = $this->cursor[$this->idx];
  }

} /* end crud class */
