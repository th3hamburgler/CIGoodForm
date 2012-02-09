<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * GoodForm
 * ---
 * Create nice flexible forms in CodeIgniter
 *
 * @licence 	MIT Licence
 * @category	Librarys 
 * @author		Jim Wardlaw
 * @link		http://stwt.co/code/goodform
 * @version 	2.0
 */ 
class Goodform {

	public	$config;
	
	private $elements	= array();
	private $fields	 	= array();
	private $namespace	= '';

	private $form_validation;
	private $parser;

	private $template = 
'<div class="control-group">
	{label}
	<div class="controls">
		{input}
		{text}
	</div>
</div>';

   /**
	* Object Constructor
	*
	* @access	private
	* @return	void
	*/
	public function __construct()
	{
		$this->load_config();
		$this->load_helpers();
		$this->load_libraries();
	}	

   /**
	* Builds the form and returns it as html
	*
	* @access	public
	* @param	mixed
	* @return	string
	*/
	public function generate($attr)
	{
		return static::html_element('form', $this->generate_elements(), $attr);
	}

   /**
	* Builds the form and returns it as html
	*
	* @access	public
	* @param	mixed
	* @return	string
	*/
	public function generate_elements()
	{
		$elements = array();
		foreach($this->fields as $name => $raw_name) {
			$elements[$name] = $this->generate_element($name);
		}
		return implode("\n", $elements);
	}

   /**
	* Builds the form and returns it as html
	*
	* @access	public
	* @param	string
	* @return	string
	*/
	public function generate_element($name)
	{
		$input; $label; $help; $warning; $error; $success;
		$attr = element($name, $this->elements, array());
		$type = element('element', $attr, 'input');
		
		$label	= $this->generate_label($attr);
		$text	= $this->generate_text($attr);
		
		switch($type) {
			case 'input':
				$input = $this->generate_input($name, $attr);
				break;
			case 'textarea':
			case 'button':
				$input = 'todo';
				break;
			case 'select':	
			case 'datalist':
				$input = 'todo';
				break;
			default:
				$input = 'todo';
				break;
		}
		
		$data = array(
			'label'	=> $label,
			'input'	=> $input,
			'text'	=> $text,
		);
		
		return $this->parser->parse_string($this->template, $data, TRUE);
	}

   /**
	* Builds an input element
	*
	* @access	public
	* @param	mixed
	* @return	string
	*/
	private function generate_input($name, $attr)
	{
		$blacklist = array('label', 'help', 'element');
		$attr = $this->clean_attributes($attr, $blacklist, TRUE);
		return static::html_element('input', FALSE, $attr);
	}

   /**
	* cleans attribute array so it only has allowed html5 attributes
	*
	* @access	public
	* @param	array		attribute key/value array
	* @param	array		attribute keys to look for
	* @param	boolean		whitelist/blacklist flag
	* @return	array
	*/
	private function clean_attributes($attr, $list=array(), $blacklist=TRUE)
	{
		if(!$blacklist) {
			foreach($attr as $k => $v) {
				if(!in_array($k, $list) AND strtolower(substr($k, 0, 5)) != 'data-') {
					unset($attr[$k]);
				}
			}
		} else {
			foreach($attr as $k => $v) {
				if(in_array($k, $list)) {
					unset($attr[$k]);
				}
			}
		}
		return $attr;
	}

	private function generate_label($field_attr)
	{
		$label	= element('label', $field_attr);
		$name	= element('name', $field_attr);
		if($label) {
			$attr = array(
				'id'	=> $name.'-label',
				'for'	=> $name,
			);
			return static::html_element('label', $label, $attr);
		}
	}

	private function generate_text($attr)
	{
		$messages = array('error', 'success', 'warning', 'help',);
		foreach($messages as $m) {
			if(element($m, $attr)) {
				$text = element($m, $attr);
				$attr = array('class' => 'help-block');
				return static::html_element('p', $text, $attr);
			}
		}
	}


   /**
	* returns an html element
	*
	* @access	public
	* @param	string		html tag name
	* @param	string		element text value
	* @param	mixed		element attributes either string or array
	* @return	string
	*/
	public static function html_element($tag, $text, $attr)
	{
		if (is_array($attr)) {
			$string = '';
			foreach ($attr as $k => $v) {
				$string .= ' ' . $k . '="' . $v . '"';
			}
			$attr = $string;
		}
		if($text === FALSE) {
			return '<'.$tag.' '.$attr.' />';
		} else {
			return '<'.$tag.' '.$attr.'>'.$text.'</'.$tag.'>';
		}
	}

   /**
	* Adds an input form element to the form
	*
	* @access	public
	* @param	mixed		field name (string) or array of field attributes
	* @param	string		field value - stored in param 1 if an array
	* @param	string		input type
	* @return	object
	*/
	public function element($name, $value=null)
	{
		$attr=array();
		if(!is_array($name)) {
			$attr['name']	= $name;
			$attr['value']	= $value;
		} else {
			$attr = $name;
		}
		$name = element('name', $attr);
		if(!$name) {
			log_message('error', 'GoodForm: name attribute missing, can not add field to form.');
			return $this;
		}
		$raw_name	= $name;
		$name		= $this->namespace.$name;
		if(element($name, $this->fields)) {
			log_message('error', 'GoodForm: a field "'.$raw_name.'" already exists in the form. Use a different field name or namespace.');
			return $this;
		}
		
		$this->fields[$name] = $raw_name;
		$this->elements[$name] = $attr;
		return $this;
	}
	
   /**
	* Adds an input form element to the form
	*
	* @access	public
	* @param	mixed		field name (string) or array of field attributes
	* @param	string		field value - stored in param 1 if an array
	* @param	string		input type
	* @return	object
	*/
	public function input($name, $value=null, $type='text')
	{
		$attr=array();
		if(!is_array($name)) {
			$attr['name']	= $name;
			$attr['value']	= $value;
		} else {
			$attr = $name;
		}
		$attr = set_element('type', $attr, $type);
		$attr = set_element('element', $attr, 'input');
		return $this->element($attr);
	}

   /**
	* Adds a text input form element to the form
	*
	* @access	public
	* @param	mixed		field name (string) or array of field attributes
	* @param	string		field value - stored in param 1 if an array
	* @return	void
	*/
	public function text($name, $value=null)
	{
		$this->input($name, $value, 'text');
	}

   /**
	* Sets a namespace for the form, this will add a prefix to all
	* field name and ids added to the form from this point forward.
	*
	* @access	public
	* @param	string
	* @return	void
	*/
	public function set_namespace($name)
	{
		$this->namespace = $name;
	}


   /**
	* Loads required config files
	*
	* @access	private
	* @return	void
	*/
	private function load_config()
	{
		if ($CI =& get_instance()) {
			$this->config = $CI->config;
		}
		$this->config->load('goodform', TRUE, TRUE);
	}

   /**
	* Loads required CodeIgniter helpers
	*
	* @access	private
	* @return	void
	*/
	private function load_helpers()
	{
		$CI =& get_instance();
		$CI->load->helper('array');
	}
	
   /**
	* Loads required CodeIgniter libaries
	*
	* @access	private
	* @return	void
	*/
	private function load_libraries()
	{
		$CI =& get_instance();
		if(!isset($CI->form_validation)) {
			$CI->load->library('form_validation');
			$CI->lang->load('form_validation');
			$CI->load->library('parser');
		}
		$this->form_validation = $CI->form_validation;
		$this->form_validation->set_error_delimiters('', '');
		$this->parser = $CI->parser;
	}

}
/**
 * Set Element
 *
 * Lets you determine whether an array index is set and whether it has a value.
 * If the element is empty the new value will be set in the returned array
 *
 * @access	public
 * @param	string
 * @param	array
 * @param	mixed
 * @return	array
 */	
if ( ! function_exists('set_element'))
{
	function set_element($item, $array, $value)
	{
		// check if element is already set
		if (!isset($array[$item]) OR $array[$item] === "")
		{
			$array[$item] = $value;
		}

		return $array;
	}	
}
/* End of file goodform.php */
/* Location: ./application/librarys/goodform.php */