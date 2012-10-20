<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Maintenance {
  public $CI;
  
  public function __construct() {
    $this->CI =& get_instance();
  }

	public function updateserversettings() {
	  $n = chr(10);	
    $text = '<?php'.$n;
    
    foreach ($this->CI->m_settings->cache as $key => $value) {
      if (is_numeric($value)) {
        $text .= '$config[\''.$key.'\'] = '.$value.';'.$n;
      } else {
        $text .= '$config[\''.$key.'\'] = \''.addcslashes($value,"'").'\';'.$n;
      }
    }
    
    /* update the serialized settings file for the server */
    $folder = $this->CI->m_settings->cache['server_folder'].'/config';
    $file = tempnam($folder,'con');
    file_put_contents($file,$text);
    rename($file,$folder.'/config.php');
	}

  public function cleanup() {
    /* setup the installer present warning */
    $this->CI->data['installer'] = (file_exists('../installer')) ? true : false;
    
    /* do first time clean up */
    if (file_exists('application/cache/firsttime')) {
      $this->updateserversettings();
      $this->CI->load->model('m_resource');
      $this->CI->m_resource->update_table();
      $this->CI->data['firsttime'] = true;
      unlink('application/cache/firsttime');
    }  
    $this->delete_old_caches();
  }

  public function delete_old_caches() {
    /* delete cache files older then the expiration + 1 hour */
    $files = glob($this->CI->m_settings->cache['server_folder'].'/cache/*');
    foreach ($files as $file) {
      if (basename($file) != 'index.html' && filemtime($file) < (time() - ($this->CI->m_settings->cache['cache_expiration'] + 3600))) {
        @unlink($file);
      }
    }  
  }
    
  public function user_access_logs() {
    /* grab the user access files and enter them */
    $this->CI->load->model('m_user');
    $files = glob($this->CI->m_settings->cache['server_folder'].'/logs/access-log-*');
    foreach ($files as $file) {
      $clean = $this->get_user_access_array($file);
      foreach ($clean as $id => $ts) {
        $this->CI->m_user->update($id,array('last_server_visit'=>$ts));
      }
    }
  }
  
  public function general_access() {
    /* grab the general access file and enter them */
    $files = glob($this->CI->m_settings->cache['server_folder'].'/logs/log-*');
    foreach ($files as $file) {
      $this->get_server_entry_array($file);
    }  
  }
  
  public function get_user_access_array($file) {
    /* we need to lock this file because we need to truncate it */
    $clean = array();
    $fp = fopen($file, "r+");
    if (flock($fp, LOCK_EX)) {
      if (filesize($file) > 0) {
        while (($line = fgets($fp,1024)) !== false) {
          $entry = unserialize($line);
          if (is_array($entry)) {
            $clean[$entry['id']] = $entry['ts'];
          }
        }
        ftruncate($fp, 0);
      }
      flock($fp, LOCK_UN);
    }
    fclose($fp);
    /* older then a day - delete */
    if (filemtime($file) < (time() - 86400)) {
      unlink($file);
    }
    return $clean;
  }  
  
  public function get_server_entry_array($file) {
    /* we need to lock this file because we need to truncate it */
    $fp = fopen($file, "r+");
    if (flock($fp, LOCK_EX)) {
      if (filesize($file) > 0) {
        while (!feof($fp)){ 
          $line = fgets($fp);
          $data = unserialize($line);
          if (is_array($data)) {
            $sql = $this->make_insert_sql($data,'gui_log');
            /* for some reason the CI insert leaks memory or something so I do it direct */
            mysql_query($sql);
            //$this->CI->m_gui_log->insert($data);
          }
        }
        ftruncate($fp, 0);
      }
      flock($fp, LOCK_UN);
    }
    fclose($fp);
    /* older then a day - delete */
    if (filemtime($file) < (time() - 86400)) {
      unlink($file);
    }
  }
  
  private function make_insert_sql($data,$table) {
    $fields = $values = '';
    foreach ($data as $key => $value) {
      $fields .= '`'.$key.'`, ';
      $values .= "'".mysql_real_escape_string($value)."', ";
    }
    return 'insert into '.$table.' ('.rtrim($fields,', ').') values ('.rtrim($values,', ').')';
  }

} /* end maintenance */