<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
Syntax is exactly the same as sessions in CI Manual

Make sure you set config values to

$config['sess_use_database']  = TRUE;
$config['sess_table_name']    = 'sessions';


******** You MUST match id varchar(#) to match config $sess_cookie_name_length = #; default 128

CREATE TABLE `sessions` (
  `id` varchar(128) NOT NULL DEFAULT '0',
  `start` int(10) unsigned NOT NULL DEFAULT '0',
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(50) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

  */
class MY_Session extends CI_Session {

  /* the length of the cookie unquie id should be between 32-255 - this MUST match the database column width */
  public $sess_cookie_name_length = 128;
  
  /* actual session data fields */
  public $session_data = array();

  /**
   * Session Constructor
   *
   * The constructor runs the session routines automatically
   * whenever the class is instantiated.
   */
  public function __construct() {
    parent::__construct();
  }

  public function __destruct() {
    /* save all the session changes to db */
    if (!empty($this->session_data['id'])) {
      $this->CI->db->where('id', $this->session_data['id']);
      $this->CI->db->update($this->sess_table_name, array(
        'data'=>serialize($this->userdata),
        'start'=>$this->session_data['start'],
        'last_activity'=>$this->session_data['last_activity'],
        'ip_address'=>$this->session_data['ip_address'],
        'user_agent'=>$this->session_data['user_agent']
      ));
    }
  }
  
  public function sess_read() {
    /* read cookie from browser sent cookie's */
    $session_id = $this->CI->input->cookie($this->sess_cookie_name);

    if (empty($session_id)) {
     log_message('debug', 'A session cookie was not found.');
     return false;
    }

    /* Does the session exist where clause. */
    $this->CI->db->where('id', $session_id);

    /* Does the IP match where clause. */
    if ($this->CI->config->item('sess_match_ip') === true) {
      $this->CI->db->where('ip_address', $this->CI->input->ip_address());
    }
    
    /* Does the user agent match where clause. */
    if ($this->CI->config->item('sess_match_useragent') === true) {
      $this->CI->db->where('user_agent', substr($this->CI->input->user_agent(), 0, 50));
    }

    /* let's run this */
    $dbc = $this->CI->db->get($this->sess_table_name);

    /* No session found or more then 1?, so destroy this session, a new one will be started */
    if ($dbc->num_rows() !== 1) {
      $this->sess_destroy();
      return false;
    }

    /* get out record */
    $row = $dbc->row();

    /* Is this session current? */
    if (($row->last_activity + $this->sess_expiration) < $this->now) {
      /* to old */
      $this->CI->db->delete($this->sess_table_name,array('id'=>$session_id));
      $this->sess_destroy();
      return false;
    }

    $userdata = @unserialize($row->data);
    $this->userdata = (is_array($userdata)) ? $userdata : array();

    $this->session_data = array(
      'id'=>$row->id,
      'start'=>$row->start,
      'last_activity'=>$row->last_activity,
      'ip_address'=>$row->ip_address,
      'user_agent'=>$row->user_agent
    );

    return true;
  }

  /* patch this out we write to the db once on destruct - I would hope this is reliable on PHP 5.3+ */
  public function sess_write() {}

  public function sess_create() {
    $this->session_data = array(
      'id'=>$this->create_token(),
      'data'=>serialize($this->userdata),
      'start'=>$this->now,
      'last_activity'=>$this->now,
      'ip_address'=>$this->CI->input->ip_address(),
      'user_agent'=>substr($this->CI->input->user_agent(), 0, 50)
    );
    $this->CI->db->insert($this->sess_table_name, $this->session_data); 
    $this->_set_cookie(); /* and set the cookie */
  }

  public function sess_update() {
    /* is this cookie/session "old" and needs a new key? */
    if ($this->now > ($this->session_data['start'] + $this->sess_time_to_update)) {
      /* delete the old one from the database */
      $this->CI->db->delete($this->sess_table_name, array('id'=>$this->session_data['id']));

      /* create a new session id WITH user data */
      $this->sess_create();
    } else {
      /*
      else just update the last activity to keep it "fresh"
      this will "auto" save when the class is closed
      */
      $this->session_data['last_activity'] = $this->now;
    }
  }

  public function sess_destroy() {
    $this->CI->db->delete($this->sess_table_name,array('id'=>$this->session_data['id']));
    $this->session_data['id'] = null;
    $this->userdata = array(); /* just incase flush this to! */

    /* Delete (empty) the session cookie. This effectively destroys the session. */
    setcookie(
      $this->sess_cookie_name,
      ' ',
      ($this->now - 31500000),
      $this->cookie_path,
      $this->cookie_domain,
      0
    );
  }

  public function _set_cookie() {
    $expire = ($this->sess_expire_on_close === TRUE) ? 0 : $this->sess_expiration + time();

    setcookie(
      $this->sess_cookie_name,
      $this->session_data['id'],
      $expire,
      $this->cookie_path,
      $this->cookie_domain,
      $this->cookie_secure
    );

  }

  private function create_token() {
    return substr(base64_encode(sha1('a'.uniqid('', true)).md5('b'.uniqid('', true)).sha1('a'.uniqid('', true))),0,$this->sess_cookie_name_length);
  }

} /* Closing bracket */

/* END Session Class */