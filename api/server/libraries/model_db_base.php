<?php if (RUNNING !== true) die();
/*
Don Myers 2012
Open Software License v. 3.0 (OSL-3.0)
http://www.opensource.org/licenses/osl-3.0.php
*/

class model_db_base extends model_root {
  public $tablename;
  public $connection;
  public $describe_table;
  public $primary_key;

  public function __construct($child=null) {
    parent::__construct($child);
    
    $this->tablename = ($this->tablename) ? $this->tablename : substr($this->name,6);
    $dbcon = ($this->connection) ? $this->connection : 'default_server_connection';
    $this->connection = $this->core->connect($dbcon);
    $this->describe_table = $this->describe_table();
    $this->primary_key = $this->get_primary();
  }

  /* describe the model */
  /* returns
  200: success
  */
  public function func_describe() {
    if ($this->core->check($this->model_name,'describe') === true) {
      parent::func_describe();
      $data['primary'] = $this->primary_key;
      $data['columns'] = $this->describe_table;
      $this->core->data[$this->model_name] = $data;
    }
  }

  /* sql select */
  /* returns
  200: records array()
  */
  public function rest_get() {
    $calc_ary = array('eq'=>'=','lt'=>'<','gt'=>'>','lk'=>'like');
    $dir_ary = array('a'=>'asc','d'=>'desc','z'=>'desc');

    $sql = "select * from `".$this->tablename."`";

    $arg = 0;
    $where = $orderby = $limit = '';

    // single id
    if ($this->core->segc() == 1) {
      $where = "where `".$this->primary_key."` = '".mysql_real_escape_string($this->core->seg($arg++))."'";
    } elseif ($this->core->segc() > 1) {
      /*
      bunch of argments
      where + 3 = column + lt,gt,eq,like + value
      orderby + 2 = column + a,d
      limit + # or limit + #,#
      */
      while ($arg <= $this->core->segc()) {
        if ($arg > 10) $this->core->badrequest($errno=903,$errtxt='Arg Overflow Error');
        
        if ($this->core->seg($arg) == 'search') {
          // where
          $field = $this->core->seg(++$arg);
          $calc = @$calc_ary[$this->core->seg(++$arg)];
          $val = $this->core->seg(++$arg);

          if ($field == NULL || $calc == NULL || $val == NULL) $this->core->badrequest($errno=900,$errtxt='search incorrectly formatted');

          $where = ' where `'.$field.'` '.$calc." '".(($calc == 'like') ? '%' : '').mysql_real_escape_string($val).(($calc == 'like') ? '%' : '')."'";
        }
        
        if ($this->core->seg($arg) == 'sort') {
          // sort order by
          $field = $this->core->seg(++$arg);          
          $dir = $dir_ary[$this->core->seg(++$arg)];
          if ($field == NULL || $dir == NULL) $this->core->badrequest($errno=901,$errtxt='sort incorrectly formatted');

          $orderby = ' order by `'.$field.'` '.$dir;
        }
        
        if ($this->core->seg($arg) == 'limit') {
          // limit # or limit #,#
          $cnt = $this->core->seg(++$arg);
          if ($cnt == NULL) $this->core->badrequest($errno=902,$errtxt='limit incorrectly formatted');
    
          $limit = ' limit '.$cnt;
        }
        
        $arg++;
      }
    }
    
    //die($sql.$where.$orderby.$limit);   
    $result = $this->core->query($sql.$where.$orderby.$limit,$this->connection);
    /* if (mysql_affected_rows() == 0) $this->core->notFound(); */

    while ($row = mysql_fetch_assoc($result)) {
      $this->core->data['records'][] = $row;
    }
  }

  /* sql insert */
  /* returns
  201: url to resource last segment is the insert id/primary id
  204: fail no rows effected
  */
  public function rest_post() {
    /* if they sent in a primary id then send it to put */
    if ($this->core->seg(0) != null) $this->rest_put();

    $sql = 'insert `'.$this->tablename.'` set '.implode(', ',$this->create_values());
    $this->core->query($sql,$this->connection);
    if (mysql_affected_rows() > 0) $this->core->created(rtrim($this->core->root_url,'/').'/'.mysql_insert_id());
    else $this->core->noContent();
  }

  /* sql update */
  /* returns
  201: url to resource last segment is the insert id/primary id
  204: fail no rows effected
  */
  public function rest_put() {
    $primary = $this->core->seg(0);
    if (empty($primary)) $this->core->badrequest(1,'primary id not provided');

    $sql = 'update `'.$this->tablename.'` set '.implode(', ',$this->create_values())." where ".$this->primary_key." = '".mysql_real_escape_string($primary)."'";
    $this->core->query($sql,$this->connection);

    if (mysql_affected_rows() > 0) $this->core->created($this->core->root_url);
    else $this->core->nocontent();
  }

  /* sql delete */
  /* returns
  201: success
  204: fail
  */
  public function rest_delete() {
    $primary = @$this->core->seg(0);
    if (empty($primary)) $this->core->badrequest(1,'primary id not provided');

    $sql = "delete from `".$this->tablename."` where ".$this->primary_key."= '".mysql_real_escape_string($primary)."'";
    $result = $this->core->query($sql,$this->connection);

    if (mysql_affected_rows() > 0) $this->core->noContent();
    else $this->core->badrequest('903','record not deleted');
  }

  /* internal function */
  private function describe_table() {
    $data = $this->core->read_cache($this->name.'describe_table');
    if ($data !== null) return $data;

    $result = $this->core->query("show columns from `".$this->tablename."`",$this->connection);

    if (mysql_num_rows($result) > 0) {
      while ($row = mysql_fetch_assoc($result)) {
        $rtn[] = $row;
      }
    }

    $this->core->write_cache($this->name.'describe_table',$rtn);
    return $rtn;
  }

  private function get_primary() {
    $data = $this->core->read_cache($this->name.'get_primary');
    if ($data !== null) return $data;

    foreach ($this->describe_table as $column) {
      if ($column['Key'] == 'PRI') {
        $this->core->write_cache($this->name.'get_primary',$column['Field']);
        return $column['Field'];
      }
    }

    return false;
  }

  private function table_fields() {
    $data = $this->core->read_cache($this->name.'table_fields');
    if ($data !== null) return $data;
    
    $columns = array();
    foreach ($this->describe_table as $column) {
      $columns[$column['Field']] = $column['Field'];
    }
    $this->core->write_cache($this->name.'table_fields',$columns);

    return $columns;
  }

  private function create_values() {
    $columns = $this->table_fields();
    
    $sql_columns = array();
    foreach ($this->core->request as $key => $value) {
      if (in_array($key,$columns,true)) {
        $sql_columns[] = "`".mysql_real_escape_string((string)$key)."` = '".mysql_real_escape_string((string)$value)."'";
      }
    }

    return $sql_columns;
  }

} /* end db_model */