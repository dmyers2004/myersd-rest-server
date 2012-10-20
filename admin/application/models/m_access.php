<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class m_access extends crud {
  public function __construct() {
    parent::__construct('access','id,name,gui_access','id','created','modified');
  }
} /* end class */
