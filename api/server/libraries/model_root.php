<?php if (RUNNING !== true) die();
/*
Don Myers 2012
Open Software License v. 3.0 (OSL-3.0)
http://www.opensource.org/licenses/osl-3.0.php
*/

class model_root {
  public $core;
  public $version;
  public $name;
  public $type;
  public $model_name;

  public function __construct($child=null) {
    $this->core =& get_instance();
    $this->name = ($this->name) ? $this->name : get_class($child);
    $this->model_name = ($this->model_name) ? $this->model_name : substr($this->name,6);
    $this->type = get_class($child);
  }

  public function func_describe() {
    if ($this->core->check($this->model_name,'describe') === true) {
      $data['type'] = $this->type;
      $data['access'] = @$this->core->userdata[$this->name];
      $this->core->data[$this->model_name] = $data;
    }
  }

  public function call_function($function_name) {
    if ($this->core->check($this->core->model_name,$function_name) !== true) {
      $this->core->notacceptable('Access Denied:'.$this->model_name.':'.$function_name);
    }

    if (!method_exists($this,'func_'.$function_name)) {
      $this->core->notacceptable('method not found '.$function_name);
    }
    
    call_user_func(array($this,'func_'.$function_name));
  }

}