<?php namespace Dakshhmehta\EmailManager;

use Config;

class EmailTemplate extends \Eloquent {
	protected $table = 'email_templates';
	protected $guarded = array();

	public static $rules = array(
		'name'	=>	'required|unique:mail_templates',
		'body'	=>	'required',
		'subject' => 'required'
	);

	// We set variables while setting body;
	public function setBodyAttribute($value)
	{
		// Extract variables from body
		preg_match_all('/##([a-zA-Z0-9-_. ]+)##/', $value, $variables, PREG_SET_ORDER);

		// Prepare the variables
		$vars = array();
		foreach($variables as $var)
		{
			if($var[1] != 'message') // Only include if its not message, its reserved by system for original message
				array_push($vars, $var[1]);
		}

		// Set values
		$this->attributes['body'] = $value;
		$this->attributes['variables'] = json_encode($vars);

		return true;
	}

	public function getVariablesAttribute($value)
	{
		$vars = array();

		foreach(json_decode($value) as $var)
		{
			// Keep the variables private that starts with _
			if(strpos($var, '_') !== 0)
				array_push($vars, $var);
		}

		return $vars;
	}

	public function field($name, $value)
	{
		$values = array();
		$multiSelect = false;
		if(is_array($value))
		{
			foreach ($value as $val) {
				$values[] = $val;
			}
			$multiSelect = true;
		}
		
		if($multiSelect == true)
		{
			Template::addRawJS('
				$(document).ready(function(){
					$("#'.$name.'").select2();
				});
			');
		}
		
		$html = '<select'.(($multiSelect == true) ? ' multiple' : '').' class="form-control" name="'.$name.(($multiSelect == true) ? '[]' : '').'" id="'.$name.'">';
		
		if($multiSelect === false)
		{
			$html .= '<option value="">-- Select --</option>';
		}

		$options = self::all();

		foreach($options as $option)
		{
			$html .= '<option '.(
				(
					($multiSelect == false) 
					? ($option->name === $value || $option->id == $value) 
					: (in_array($option->name, $values) || in_array($option->id, $values))
				) 
				? 'selected ' 
				: '').'value="'.$option->id.'" data-subject="'.htmlspecialchars($option->subject).'" data-body="'.htmlspecialchars($option->body).'" data-variables="'.implode(',', $option->variables).'">'.$option->name.'</option>';
		}

		$html .= '</select>';

		return $html;

	}
}
