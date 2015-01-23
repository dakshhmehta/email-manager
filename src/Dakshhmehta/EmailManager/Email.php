<?php namespace Dakshhmehta\EmailManager;

use Config;
use Eloquent;

class Email extends Eloquent {
	protected $table = Config::get('email-manager::mails_table');

	protected $guarded = array();

	public function user(){
		return $this->belongsTo('User');
	}

	public function scopeTo($query, $email){
		$query->where('to', '=', $email);
	}
}
