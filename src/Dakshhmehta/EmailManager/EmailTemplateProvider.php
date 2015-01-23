<?php namespace Dakshhmehta\EmailManager;

use Dakshhmehta\EmailManager\Repositories\EmailTemplateRepository;

class EmailTemplateProvider extends EmailTemplate implements EmailTemplateRepository {
	public function variables(){
		return json_decode($this->original['variables']);
	}

	public function all(){
		return self::all();
	}

	public function create(array $data){
		return self::create($data);
	}

	public function find($id){
		return self::findOrFail($id);
	}

	public function update($id, array $data){
		$template = $this->find($id);

		return $template->update($data);
	}

	public function delete($id){
		$template = $this->find($id);

		if($template and $template->system == false)
			return $template->delete();

		return false;
	}
}