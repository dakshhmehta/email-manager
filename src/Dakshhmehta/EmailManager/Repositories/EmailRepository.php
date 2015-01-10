<?php namespace Dakshhmehta\EmailManager\Repositories;

use Dakshhmehta\EmailManager\EmailTemplate;

interface EmailRepository {
	public function send($input, EmailTemplate $template, $variables, $modified = false);
	
}