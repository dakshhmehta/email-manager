<?php namespace Dakshhmehta\EmailManager;

use Dakshhmehta\EmailManager\Repositories\EmailTemplateRepository;
use Dakshhmehta\EmailManager\EmailTemplate;

class EmailTemplateProvider implements EmailTemplateRepository {
	protected $model;

	public function __construct(EmailTemplate $model)
	{
		$this->model = $model;
	}

	public function all(){
		return EmailTemplate::all();
	}

	public function create(array $data){
		$template = $this->model->newInstance();

		$template->name = $data['name'];
		$template->subject = $data['subject'];
		$template->body = $data['body'];

		if(isset($data['system']))
		{
			$template->system = $data['system'];
		}

		if($template->save()) return $template;

		return false;
	}

	public function find($id){
		return EmailTemplate::findOrFail($id);
	}

	public function update($id, array $data){
		$template = $this->model->findOrFail($id);

		$template->subject = $data['subject'];
		$template->body = $data['body'];

		if(isset($data['system']))
		{
			$template->system = $data['system'];
		}		

		if($template->system == 0)
		{
			$template->name = $data['name'];
		}

		if($template->save()) return $template;

		return false;
	}

	public function delete($id){
		$template = $this->find($id);

		if($template and $template->system == false)
			return $template->delete();

		return false;
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

		$options = $this->all();

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

	public function getByName($name)
	{
		return EmailTemplate::where('name', '=', $name)->firstOrFail();
	}
}