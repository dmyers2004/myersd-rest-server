<?php if (RUNNING !== true) die();
/*
Don Myers 2012
Open Software License v. 3.0 (OSL-3.0)
http://www.opensource.org/licenses/osl-3.0.php
*/

class model_cookies extends model_db_base {
  public $version = '1.2';
  public $name = 'cookies';
  public $tablename = 'default_settings';
  public $connection = 'server_a';

  public function __construct() {
    parent::__construct($this);
  }

  public function func_cookies() {
  
  }

} /* end class */