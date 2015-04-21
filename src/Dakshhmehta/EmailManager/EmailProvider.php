<?php namespace Dakshhmehta\EmailManager;

use Dakshhmehta\EmailManager\EmailTemplate;
use Dakshhmehta\EmailManager\Repositories\EmailRepository;
use Mail;
use Debugbar;

class EmailProvider extends Email implements EmailRepository {
	// @todo Need better solution for email parser
	public function send($input, EmailTemplate $emailTemplate = null, $mailVariables = array(), $modified = false)
	{
		// If template is selected
		if($emailTemplate != null)
		{
			// Prepare the original message with it's variables
			$variables = $emailTemplate->variables();

			// Set the blank body if not specified,
			if(! isset($input['body'])){
				$input['body'] = null;
			}

			// If modified, try to use input body as original body!
			if($modified == true){
				$input['body'] = $input['body'];
			}
			else {
				$input['body'] = str_replace('##message##', $input['body'], $emailTemplate->body);
			}

			// Change the subject
			if(! isset($input['subject']))
			{
				$input['subject'] = $emailTemplate->subject;
			}

			// Bind the signature of user
			//$input['body'] = str_replace('##_user_signature##', Sentry::getUser()->signature, $input['body']);

			if(isset($variables) && count($variables) > 0){
				// Okay, we have vars in an email. Lets loop through it
				foreach($variables as $variable)
				{
					$value = '';
					$pos = strpos($variable, '_');
					
					// It must be normal variable and available in values
 					if(isset($mailVariables[$variable])){
 						// There we go
 						$value = $mailVariables[$variable];
 					}

 					// If it's system variable
 					else if($pos == 0){
						$var = substr($variable, 1); // Trim the suffix "_"

						// Yes, it is. lets take key's value
 						$key = explode('.', $var);
 						if(is_object($mailVariables[$key[0]])){
 							Debugbar::addMessage('Preparing system object value'.$key[0], 'debug');

 							try {
 	 							$value = $mailVariables[$key[0]]->{$key[1]};
 	 						} catch(\Exception $e){
 	 							Debugbar::addException($e);
	 							throw new \Exception("No/Invalid key specified for system variable [".$key[0]."]");
 	 						}
	 					}
	 					else {
	 						$value = $mailVariables[$key[0]][$key[1]];
	 					}
 					}

 					// For Subject
 					try {
						$input['subject'] = str_replace('##'.$variable.'##', $value, $input['subject']);
					}
					catch(\Exception $e){
						Debugbar::addException($e);
						throw new \Exception($variable.' is having wrong value');
					}

 					// For Body
 					try {
						$input['body'] = str_replace('##'.$variable.'##', $value, $input['body']);
					}
					catch(\Exception $e){
						Debugbar::addException($e);
						throw new \Exception($variable.' is having wrong value');
					}
				}
			}
		}

		// We got data, try to send email
		$email = Mail::queue('emails.blank', $input, function($m) use($input, $emailTemplate)
		{
			if(isset($input['from']))
				$m->from($input['from']['email'], $input['from']['name']);

			$m->to($input['to']);

			if($emailTemplate != null){
				$m->subject($emailTemplate->subject);
			}
			else {
				$m->subject($input['subject']);
			}

			if(isset($input['cc']))
			{
				$m->cc($input['cc']);
			}

			if(isset($input['bcc']))
			{
				$m->bcc($input['bcc']);
			}
		});

		return true;
	}

}