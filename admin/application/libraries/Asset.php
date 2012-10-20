<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
  $this->asset->header() - output full header
  
  $this->asset->link(href/array,array); - CSS
  $this->asset->script(file/array,array); - SCRIPT
  $this->asset->meta(name/array,content,array); - Meta Tags
  $this->asset->extra(string); - anything else
*/
class Asset {
  private $CI;

  public $js = array();
  public $css = array();
  public $meta = array();
  public $extra = array();

  public $folder;
  public $css_folder;
  public $js_folder;

  public $default_css;
  public $default_js;
  public $default_meta;
  
  public function __construct() {
    $this->CI = get_instance();
    $this->CI->data['asset'] = $this;

    $this->CI->load->config('asset',TRUE);

    $this->folder = $this->config('folder','assets/');
    $this->css_folder = $this->config('css_folder','assets/css/');
    $this->js_folder = $this->config('js_folder','assets/js/');

    $this->default_css = $this->config('default_css',array('rel'=>'stylesheet','type'=>'text/css','href'=>''));
    $this->default_js = $this->config('default_js',array('src'=>''));
    $this->default_meta = $this->config('default_meta',array('name'=>'','content'=>''));

    $autoload_css = $this->config('autoload_css',array());
    $autoload_js = $this->config('autoload_js',array());
    $autoload_meta = $this->config('autoload_meta',array());
       
    /* run the autoloaders */
    foreach ($autoload_css as $css)
      $this->link($css);
    foreach ($autoload_js as $js)
      $this->script($js);
    foreach ($autoload_meta as $meta)
      $this->meta($meta);      
      
  }
  
  public function config($name,$default) {
    $temp = $this->CI->config->item($name, 'asset');
    return ($temp) ? $temp : $default;
  }
  
  public function header() {
    return implode(chr(10),$this->meta).chr(10).implode(chr(10),$this->css).chr(10).implode(chr(10),$this->js).chr(10).implode(chr(10),$this->extra).chr(10);
  }
  
  public function link($href='',$append=array()) {
    $merged = (is_array($href)) ? $href : array_merge($this->default_css,array('href'=>$this->prefix($href,$this->css_folder)),$append);
    $this->css[md5(serialize($merged))] = '<link '.$this->_ary2attr($merged).' />';
  }

  public function script($file='',$append=array()) {
    $merged = (is_array($file)) ? $file : array_merge($this->default_js,array('src'=>$this->prefix($file,$this->js_folder)),$append);
    $this->js[md5(serialize($merged))] = '<script '.$this->_ary2attr($merged).'></script>';
  }
  
  public function meta($name='',$content='',$append=array()) {  
    $merged = (is_array($name)) ? $name : array_merge($this->default_meta,array('name'=>$name,'content'=>$content),$append);
    $this->meta[md5(serialize($merged))] = '<meta '.$this->_ary2attr($merged).'>';
  }

  public function extra($extra='') {
    $this->extra[md5(serialize($extra))] = $extra;
  }
    
  private function _ary2attr($array) {
    $output = '';
    foreach ((array)$array as $name => $value) {
      $output .= $name.'="'.$value.'" ';
    }
    return trim($output);
  }

  private function prefix($input,$asset) {
    if (substr($input,0,4) == 'http') {
      return $input;
    } elseif ($input{0} == '/') {
      return base_url().$this->folder.$input;
    } else {
      return base_url().$asset.$input;
    }
  }
  
} /* end class */