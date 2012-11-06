<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class controller_modules extends MY_Admin_controller {

  public function __construct() {
    parent::__construct();
    
    $this->load->model('m_resource');
    $this->m_resource->update_table();
  }

  /* view */
  public function index() {
    $this->load->helper('view');
    
    $this->data['resource'] = $this->m_resource->read2array(); 
    $this->render();
  }
  
  /* view */
  public function create() {
    $this->load->helper('form');
    $this->render();
  }
  
  /* redirect / die */
  public function process_create() {
    $content = $this->db_model_template();
    
    $name = $this->input->post('name');
    $tablename = $this->input->post('tablename');
    $connection = $this->input->post('connection');
        
    if (!empty($tablename)) {
      $tablename = "  public \$tablename = '".$tablename."';".chr(10);
    }
    
    if (!empty($connection)) {
      $connection = "  public \$connection = '".$connection."';".chr(10);
    }
    
    $content = str_replace('{name}',$name,$content);
    $content = str_replace('{tablename}',$tablename,$content);
    $content = str_replace('{connection}',$connection,$content);

    if ($this->input->post('btn') == 'install') {
      $file = $this->m_settings->cache['server_folder'].'models/'.$name.'.php';
      if (!file_exists($file)) {
  			$this->m_gui_log->gui_entry('modules','Created &amp; Installed',$name);
        $this->flash_msg->green('Module Installed');
        file_put_contents($file,$content);
      } else {
        $this->flash_msg->red('Module Already Exists');
      }
      redirect('modules');
    } else{
			$this->m_gui_log->gui_entry('modules','Created &amp; Downloaded',$name);
      header("Content-Type: application/force-download"); 
      header('Content-Description: File Transfer'); 
      header('Content-disposition: attachment; filename='.$name.'.php');
      die($content);
    }
  }

  /* view */
  public function upload() {
    $this->load->helper('form');
    $this->render();
  }
  
  /* redirect */
  public function process_upload() {
    $redirect_to = '/modules';

    if ($this->input->post('btn') == 'upsert') {
      $uploads_dir = $this->m_settings->cache['server_folder'].'models';
      if ($_FILES['upload']['error'] == UPLOAD_ERR_OK && $_FILES['upload']['type'] == 'text/php') {
        $tmp_name = $_FILES['upload']['tmp_name'];
        $name = $_FILES['upload']['name'];
        move_uploaded_file($tmp_name, $uploads_dir.'/'.$name);
        $this->m_gui_log->gui_entry('modules','process_upload',$name);
        $this->flash_msg->green('Module Uploaded');
      } else {
        $this->flash_msg->yellow('There was an error trying to upload your file.<br>Please check your file and try again.');
        $redirect_to = '/modules/upload';
      }
    }

    redirect($redirect_to);
  }

  /* ajax */
  public function delete($name='') {
		if ($this->input->is_ajax_request()) {
			$name = str_replace('/','',$name);
			$file = $this->m_settings->cache['server_folder'].'models/'.substr($name,6).'.php';
			if (file_exists($file)) {
				@unlink($file);
				$this->m_gui_log->gui_entry('modules','delete',$name);
        $this->flash_msg->green('Module Deleted');
			}
		}
	}

  /* die / ? */
  public function download($name='') {
		$name = str_replace('/','',$name);
		$file = $this->m_settings->cache['server_folder'].'models/'.substr($name,6).'.php';
		if (file_exists($file)) {
			$this->m_gui_log->gui_entry('modules','downloaded',$name);
      header("Content-Type: application/force-download"); 
      header('Content-Description: File Transfer'); 
      header('Content-disposition: attachment; filename='.$name.'.php');
      readfile($file);
      die();
		} else {
      $this->flash_msg->red('Module Not Found');
		}
	}
	
	/* function */
	public function db_model_template() {
	  $rtn = '<?php if (RUNNING !== true) die();
/*
Don Myers 2012
Open Software License v. 3.0 (OSL-3.0)
http://www.opensource.org/licenses/osl-3.0.php
*/

class model_{name} extends model_db_base {
  public $version = \'1.0\';
{connection}{tablename}
  public function __construct() {
    parent::__construct($this);
  }

} /* end model_{name} class */';
    return $rtn;	
	}

} /* end class */
