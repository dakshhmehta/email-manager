<?php

class Email extends JsModel {
	protected $table = 'mails';

	protected $guarded = array();

	public static $rules = array();

	public static $relationsData = array(
		'user'		=>	array(self::BELONGS_TO, 'User')
	);

	public function company()
	{
		// @todo Test this
		return $this->belongsTo('Company', 'to', 'email');
	}

	public function scopeTo($query, $email){
		$query->where('to', '=', $email);
	}
}
