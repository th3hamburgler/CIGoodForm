<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['templates']['basic'] = array(
	'default'	=> '<div class="control-group {state}">{label}{input}{text}</div>',
	'help'		=> '<span class="help-inline">{text}</span>',
	'input'		=> '{label}{input}{text}',
	'checkbox'	=> '<div class="control-group {state}">{label}<label class="checkbox">{input}{text}</label></div>',
	'radio'		=> '<div class="control-group {state}">{label}<label class="radio">{input}{text}</label></div>',	
);

$config['templates']['horizontal'] = array(
	'default'	=> '<div class="control-group {state}">
	{label}
	<div class="controls">
		{input}
		{text}
	</div>
</div>',
	'help'		=> '<span class="help-inline">{text}</span>',
	'input'		=> '{label}{input}{text}',
	'checkbox'	=> '<div class="control-group {state}">
	{label}
	<div class="controls">
		<label class="checkbox">{input}{text}</label>
	</div>
</div>',
	'radio'		=> '<div class="control-group {state}">
	{label}
	<div class="controls">
		<label class="radio">{input}{text}</label>
	</div>
</div>',
);