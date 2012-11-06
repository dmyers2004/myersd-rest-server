<?php if (RUNNING !== true) die();
/*
Don Myers 2012
Open Software License v. 3.0 (OSL-3.0)
http://www.opensource.org/licenses/osl-3.0.php
*/

class model_pyro_modules extends model_db_base {
  public $version = '1.0';
  public $tablename = 'default_settings';
  public $connection = 'server_a';

  public function __construct() {
    parent::__construct($this);
  }

} /* end class */