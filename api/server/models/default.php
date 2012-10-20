<?php if (RUNNING !== true) die();
/*
Don Myers 2012
Open Software License v. 3.0 (OSL-3.0)
http://www.opensource.org/licenses/osl-3.0.php
*/

class model_default extends model_function_base {
  public $version = '1.0';

  public function __construct() {
    parent::__construct($this);
  }

  public function rest_get() {
    /* these models are setup to use mysql database for access load each model and call it's describe method */
    $this->core->data['version'] = $this->core->config['server_version'];

    foreach ($this->core->userdata as $model => $methods) {
      if (substr($model,0,6) == 'model_') {
        $filename = substr($model,6);
        if (!class_exists($model) && file_exists('server/models/'.$filename.'.php'))
          require('server/models/'.$filename.'.php');

        if (class_exists($model)) {
          $m = new $model();
          call_user_func(array($m,'func_describe'));
        }
      }
    }
  }

} /* end class */