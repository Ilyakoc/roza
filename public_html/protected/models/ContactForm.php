<?php

/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class ContactForm extends CFormModel
{
	public $name;
	public $email;
	public $subject;
	public $body;
    public $phone;
	public $verifyCode;

    /**
     * Declares the validation rules.
     *
     * @return array
     */
	public function rules()
	{
		return array(
            array('verifyCode', 'compare', 'compareValue'=>'test_ok'),
			// name, email, subject and body are required
			array('name, email, body', 'required'),
			// email has to be a valid email address
			array('email', 'email'),
            array('phone', 'safe')
			// verifyCode needs to be entered correctly
			//array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
		);
	}

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
     *
     * @return array
	 */
	public function attributeLabels()
	{
		return array(
            'name'=>'Ваше имя',
            'subject'=>'Тема',
            'body'=>'Текст сообщения',
			'verifyCode'=>'Проверочный код',
            'phone'=>'Телефон'
		);
	}
}
