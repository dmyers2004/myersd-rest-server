<?php if (RUNNING !== true) die();
/*
Don Myers 2012
Open Software License v. 3.0 (OSL-3.0)
http://www.opensource.org/licenses/osl-3.0.php
*/

class model_login extends model_function_base {
  public $version = '1.0';

  public function __construct() {
    parent::__construct($this);
  }

  public function rest_post() {
    /*
    this takes in the default login and password and 
    checks to see if a valid user exists for email & password (passed)
    if it does then it returns a 200 and json email & double hashed password
    if it doesn't it returns a 404
    */
    //$this->core->data['key'] = $this->core->support->login(@$this->core->request['username'],@$this->core->request['password']);
    $this->core->data['key'] = md5(time());
  }
  
  public function rest_put() {
    

  
  }

} /* end class */