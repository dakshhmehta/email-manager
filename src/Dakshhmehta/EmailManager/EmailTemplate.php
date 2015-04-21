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

	public function variables(){
		return json_decode($this->original['variables']);
	}
}
