<?php if (RUNNING !== true) die();
/*
Don Myers 2012
Open Software License v. 3.0 (OSL-3.0)
http://www.opensource.org/licenses/osl-3.0.php
*/

class model_func extends model_function_base {
  public $version = '1.0';

  public function __construct() {
    parent::__construct($this);
  }

  public function rest_post() {
    $function_name = array_shift($this->core->segs);
    $this->call_function($function_name);
  }

  public function func_substr() {
    $a = $this->core->request('a','');
    $b = $this->core->request('b',0);
    $c = $this->core->request('c',0);

    $this->core->data['output'] = substr($a,$b,$c);
  }

} /* end class */