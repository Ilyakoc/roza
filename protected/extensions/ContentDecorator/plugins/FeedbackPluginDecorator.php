<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 04.10.11
 * Time: 13:08
 */
 
class FeedbackPluginDecorator extends PluginDecorator
{
    public $point = '{form_feedback}';
    
    public function processModel($model, $attribute = 'text')
    {
        $result = $this->checkPoint($model->$attribute);
        if (!$result)
            return;

        $form = $this->contactForm();

        if ($form['status'])
            $to = $form['form'];
        else
            $to = str_replace($this->point, $form['form'], $model->$attribute);

        $model->$attribute = $to;
    }

    /**
     * Processing feedback form
     * @return array
     */
    private function contactForm()
    {
		$model = new ContactForm;

		if (isset($_POST['ContactForm'])) {
			$model->attributes=$_POST['ContactForm'];

            if ($model->validate()) {
                $email = Yii::app()->email;

                $email->from    = 'noreply@'. Yii::app()->request->getServerName();
                $email->to      = Yii::app()->params['adminEmail'];
                $email->subject = 'Новое сообщение с сайта '. Yii::app()->name; //$model->subject;
                $email->message = Yii::t(
                    'email',
                    'contact_email',
                    array(
                        '{username}'=>$model->name,
                        '{email}'=>$model->email,
                        '{phone}'=>$model->phone,
                        '{message}'=>$model->body
                    )
                );

                $ok = $email->send();

                if ($ok)
				    Yii::app()->user->setFlash('contact', 'Спасибо, ваше письмо отправлено!');
                else
                    Yii::app()->user->setFlash('contact', 'При отправке письма произошла ошибка!');
			}
		}

        return array(
            'form' => Yii::app()->getController()->renderPartial('_form_contact',array('model'=>$model), true),
            'status' => isset($ok) ? true : false
        );
    }
}
