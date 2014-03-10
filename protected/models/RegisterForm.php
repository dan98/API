<?php
class RegisterForm extends CFormModel
{
	public $name;
	public $email;
	public $description;

	public function rules()
	{
		return array(
			array('name, email, description', 'required'),
			array('email', 'email'),
		);
	}
}