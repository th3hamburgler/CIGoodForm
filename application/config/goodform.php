<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['templates']['basic'] = array(
	'default'	=> '{label}{input}{text}',
	'help'		=> '<span class="help-inline">{text}</span>',
	'input'		=> '{label}{input}{text}',
	'checkbox'	=> '<label class="checkbox">{input} {label_text}</label>',
	'radio'		=> '<label class="radio">{input} {label_text}</label>',	
);
$config['templates']['horizontal'] = array();