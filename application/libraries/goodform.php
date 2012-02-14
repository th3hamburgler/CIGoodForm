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
	private $open_fieldset	= false;

	private $template_theme = 'basic';
	private $templates		= array();

   /**
	* Object Constructor
	*
	* @access	private
	* @param	array
	* @return	void
	*/
	public function __construct($config=array())
	{
		$this->load_config();
		$this->load_helpers();
		$this->load_libraries();
		$this->initialize($config);
	}	

   /**
	* Initialise instance config variables
	*
	* @access	public
	* @param	void
	* @return	void
	*/
	public function initialize($config=array())
	{
		$vars = array('template_theme');
		
		foreach($vars as $var) {
			if(element($var, $config)) {
				$this->{$var} = element($var, $config);
			}
		}
		
		return $this->initialise_templates($this->template_theme);
	}

   /**
	* Initialises multiple templates from a theme
	* stored in the config file
	*
	* @access	public
	* @param	string
	* @return	void
	*/
	public function initialise_templates($theme)
	{
		$template_keys = array(
			'default',
			'help',
			'input',
			'checkbox',
			'radio',
			'button',
		);
		$templates = element($theme, $this->config->item('templates', 'goodform'), array());
		if(!$templates) {
			log_message('error', 'GoodForm: could not find templates in config for theme "'.$theme.'"');
		} else {
			foreach($template_keys as $k) {
				$this->templates[$k] = element($k, $templates);
			}
		}
		return $this;
	}

   /**
	* initialises templates for horizontal form layouts
	*
	* @access	public
	* @param	void
	* @return	void
	*/
	public function horizontal_form()
	{
		return $this;
	}

   /**
	* Builds the form and returns it as html
	*
	* @access	public
	* @param	mixed
	* @return	string
	*/
	public function generate($attr=array())
	{
		return static::html_element('form', $this->generate_elements(), $attr);
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

		// turn class string into array
		//$attr['class'] = implode(' ', element('class', $attr, array()));

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
		return $this->input($name, $value, 'text');
	}

   /**
	* Adds a checkbox input form element to the form
	*
	* @access	public
	* @param	mixed		field name (string) or array of field attributes
	* @param	string		field value - stored in param 1 if an array
	* @return	void
	*/
	public function checkbox($name, $value=null)
	{
		return $this->input($name, $value, 'checkbox');
	}

   /**
	* Adds a radio input form element to the form
	*
	* @access	public
	* @param	mixed		field name (string) or array of field attributes
	* @param	string		field value - stored in param 1 if an array
	* @return	void
	*/
	public function radio($name, $value=null)
	{
		return $this->input($name, $value, 'radio');
	}
	
   /**
	* Adds a textarea element to the form
	*
	* @access	public
	* @param	mixed		field name (string) or array of field attributes
	* @param	string		field value - stored in param 1 if an array
	* @return	void
	*/
	public function textarea($name, $value=null)
	{
		$attr=array();
		if(!is_array($name)) {
			$attr['name']	= $name;
			$attr['value']	= $value;
		} else {
			$attr = $name;
		}
		$attr = set_element('element', $attr, 'textarea');
	
		return $this->element($attr);
	}

   /**
	* Adds a button element to the form
	*
	* @access	public
	* @param	mixed		field name (string) or array of field attributes
	* @param	string		field value - stored in param 1 if an array
	* @return	void
	*/
	public function button($name, $value=null)
	{
		$attr=array();
		if(!is_array($name)) {
			$attr['name']	= $name;
			$attr['value']	= $value;
		} else {
			$attr = $name;
		}
		$attr = set_element('element', $attr, 'button');
	
		return $this->element($attr);
	}

   /**
	* Adds a select element to the form
	*
	* @access	public
	* @param	mixed		field name (string) or array of field attributes
	* @param	string		field value - stored in param 1 if an array
	* @return	void
	*/
	public function select($name, $value=null)
	{
		$attr=array();
		if(!is_array($name)) {
			$attr['name']	= $name;
			$attr['value']	= $value;
		} else {
			$attr = $name;
		}
		$attr = set_element('element', $attr, 'select');

		return $this->element($attr);
	}
		
   /**
	* Adds a label element to the form
	*
	* @access	public
	* @param	void
	* @return	void
	*/
	public function label($label, $for=null)
	{
		$attr=array();
		if(!is_array($label)) {
			$attr['text']	= $label;
			$attr['for']	= $for;
		} else {
			$attr = $name;
		}
		$attr = set_element('element', $attr, 'label');
		$attr = set_element('name', $attr, element('for', $attr, element('label', $attr)));
		return $this->element($attr);
	}

   /**
	* Adds a label element to the form
	*
	* @access	public
	* @param	void
	* @return	void
	*/
	public function fieldset($legend, $attr=array())
	{
		$this->close_fieldset();
		$this->open_fieldset = TRUE;
		if (is_array($legend)) {
			$attr = $legend;
			if (element('legend', $attr)) {
				$legend = element('legend', $attr, null);
				unset($attr['legend']);
			} else {
				$legend = null;
			}
		}
		$this->html('<fieldset '.static::html_attributes($attr).'>');
		if ($legend) { 
			return $this->legend($legend);
		}
		return $this;
	}

   /**
	* Adds a label element to the form
	*
	* @access	public
	* @param	void
	* @return	void
	*/
	public function close_fieldset()
	{
		if (!$this->open_fieldset) return $this;
		$this->open_fieldset = FALSE;
		return $this->html('</fieldset>');
	}

   /**
	* creates a legend element in the form
	*
	* @access	public
	* @param	string
	* @param	array
	* @return	object
	*/
	public function legend($label, $attr=array())
	{
		if (!is_array($label)) {
			$attr['value']		= $label;
			$attr['element']	= 'legend';
		} else {
			$attr = $label;
			$attr['element']	= 'legend';
		}
		$attr = set_element('name', $attr, element('value', $attr).'-legend');
		
		return $this->element($attr);
	}
	
   /**
	* Adds a custom html string to the form
	*
	* @access	public
	* @return	object
	*/
	public function html($string)
	{
		// add to objects element array
		$this->elements[] = array('element' => 'html', 'html' => $string);
		return $this;
	}

   /**
	* Builds the form and returns it as html
	*
	* @access	public
	* @param	mixed
	* @return	string
	*/
	public function generate_elements($fields=null)
	{
		if(!$fields) {
			$fields = array_keys($this->elements);
		}
		$elements = array();
		foreach($fields as $name) {
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
		$attr = element($name, $this->elements, array());
		$element = element('element', $attr, 'input');
		
		switch($element) {
			case 'input':
				return $this->generate_input($name, $attr);
				break;
			case 'label':
				$html	= element('text', $attr);
				unset($attr['text']);
				return static::html_element($element, $html, $attr);
				break;
			case 'textarea':
			case 'button':
			case 'legend':
				return $this->generate_textarea($name, $attr);
				break;
			case 'select':	
			case 'datalist':
				$attr['value'] = $this->generate_options($attr);
				return $this->generate_textarea($name, $attr);
				break;
			case 'html':
				return element('html', $attr, 'asdas');
				break;
			default:
				return 'todo';
				break;
		}
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
		$label		= $this->generate_label($attr);
		$label_text	= element('label', $attr);
		$text		= $this->generate_text($attr);
		$state		= $this->get_state($attr);
		$type		= element('type', $attr, 'default');

		$template	= element('template', $attr, element($type, $this->templates, element('default', $this->templates)));
		$blacklist	= array('element', 'label', 'help', 'template',);
		$attr		= $this->clean_attributes($attr, $blacklist, TRUE);
		$input		= static::html_element('input', FALSE, $attr);
		
		$data = array(
			'state'			=> $state,
			'label'			=> $label,
			'label_text'	=> $label_text,
			'input'			=> $input,
			'text'			=> $text,
		);
		return $this->parser->parse_string($template, $data, TRUE);
	}

   /**
	* Builds a textarea element
	*
	* @access	public
	* @param	mixed
	* @return	string
	*/
	private function generate_textarea($name, $attr)
	{
		$label		= $this->generate_label($attr);
		$label_text	= element('label', $attr);
		$text		= $this->generate_text($attr);
		$state		= $this->get_state($attr);
		$element	= element('element', $attr, 'default');
		$value		= element('value', $attr, '');
		// if textarea remove value attribute
		if($element AND isset($attr['value'])) {
			unset($attr['value']);
		}
		$template	= $this->get_template($attr, $element);
		$blacklist	= array('element', 'label', 'help', 'template', 'selected', 'options');
		$attr		= $this->clean_attributes($attr, $blacklist, TRUE);
		$input		= static::html_element($element, $value, $attr);

		$data = array(
			'state'			=> $state,
			'label'			=> $label,
			'label_text'	=> $label_text,
			'input'			=> $input,
			'text'			=> $text,
		);
		return $this->parser->parse_string($template, $data, TRUE);
	}

   /**
	* Builds a group of option elements
	*
	* Options are defined in $attr['options'] with the key
	* as the label and value as the option value.
	*
	* Selected element are either defined in $attr['value']
	* or $attr['selected'] but not both. Multiple selected 
	* elements can be defined as an array of values.
	*
	* @access	private
	* @param	array
	* @return	string
	*/
	private function generate_options($attr)
	{
		$options = element('options', $attr);
		if($options === FALSE) return;
		$selected = element('value', $attr, element('selected', $attr, ''));
		if(!is_array($selected)) $selected = array($selected);
		$opt_arr = array();
		foreach ($options as $name => $v) {
			if (is_array($v)) {
				$opt_arr[] = '<optgroup label="'.$name.'">';
					$optgroup = array(
						'options'	=> $v,
						'value'		=> $selected,
					);
					// recurse!
					$opt_arr[] = $this->generate_options($optgroup);
				$opt_arr[] = '</optgroup>';
			} else {
				if (in_array($v, $selected) OR $v == $selected) {
					$opt_arr[] = '<option value="'.$v.'" selected="selected">'.$name.'</option>';
				} else {
					$opt_arr[] = '<option value="'.$v.'">'.$name.'</option>';
				}
			}
		}
		return implode("\n\t", $opt_arr);
	}

   /**
	* There is are three places an elements template can be
	* defined. In priority order:
	* - As a string in $attr['template']
	* - In the global $templates array as the element type e.g.
	*	- $this->templates['button']
	* 	- $this->templates['checkbox']
	* - The default template $this->templates['default']
	*
	* @access	private
	* @param	array
	* @param	string
	* @return	string
	*/
	private function get_template($attr, $type='default')
	{
		return 
		element(
			'template', 
			$attr, 
			element(
				$type,
				$this->templates, 
				element(
					'default',
					$this->templates
				)
			)
		);
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

	private function generate_label_attributes($field_attr)
	{
		$label	= element('label', $field_attr);
		$name	= element('name', $field_attr);
		if($label) {
			$attr = array(
				'id'	=> $name.'-label',
				'for'	=> $name,
			);
			return static::html_attributes($attr);
		}
	}

	private function generate_text($attr)
	{
		$messages = array('error', 'success', 'warning', 'help',);
		foreach($messages as $m) {
			if(element($m, $attr)) {
				$template = element('help', $this->templates, '{text}');
				return $this->parser->parse_string($template, array('text'=>element($m, $attr)), TRUE);
			}
		}
	}

   /**
	* Returns the current state of the element
	* - error		error with field value
	* - success		field value is valid
	* - warning		warning about field value
	* - null		normal
	*
	* @access	private
	* @param	array
	* @return	string
	*/
	private function get_state($attr)
	{
		$messages = array('error', 'success', 'warning',);
		foreach($messages as $m) {
			if(element($m, $attr)) {
				return $m;
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
		$attr = static::html_attributes($attr)
		;
		if($text === FALSE) {
			return '<'.$tag.' '.$attr.' />';
		} else {
			return '<'.$tag.' '.$attr.'>'.$text.'</'.$tag.'>';
		}
	}

   /**
	* converts an associative array to an html attribute array
	*
	* @access	public
	* @param	array
	* @return	string
	*/
	public static function html_attributes($attr)
	{
		if (is_array($attr)) {
			$string = '';
			foreach ($attr as $k => $v) {
				$string .= ' ' . $k . '="' . $v . '"';
			}
			$attr = $string;
		}
		return $attr;
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