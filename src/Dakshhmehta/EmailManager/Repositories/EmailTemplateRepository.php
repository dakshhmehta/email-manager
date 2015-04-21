<?php namespace Dakshhmehta\EmailManager\Repositories;

interface EmailTemplateRepository {
	public function all();
	public function create(array $data);
	public function find($id);
	public function update($id, array $data);
	public function delete($id);
	public function field($name, $value);
	public function getByName($name);
}