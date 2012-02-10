<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['templates']['basic'] = array(
	'default'	=> '{label}{input}{text}',
	'help'		=> '<span class="help-inline">{text}</span>',
	'input'		=> '{label}{input}{text}',
	'checkbox'	=> '{label}<label class="checkbox">{input}{text}</label>',
	'radio'		=> '{label}<label class="radio">{input}{text}</label>',	
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