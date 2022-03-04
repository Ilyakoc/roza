<?php

/**
 * This is the model class for table "Feedback".
 *
 * The followings are the available columns in table 'Feedback':
 * @property integer $id
 * @property string $username
 * @property string $mail
 * @property string $question
 * @property string $answer
 * @property string $published
 * @property string $created
 */
class Feedback extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Feedback the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'feedback';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, question, published, created', 'required'),
			array('username, mail, question', 'length', 'max'=>255),
			array('mail', 'email'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, username, mail, question, answer, published, created', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => 'Имя',
			'mail' => 'E-Mail',
			'question' => 'Текст сообщения',
			'answer' => 'Ответ',
			'published' => 'Опубликован',
			'created' => 'Создан',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('mail',$this->mail,true);
		$criteria->compare('question',$this->question,true);
		$criteria->compare('answer',$this->answer,true);
		$criteria->compare('published',$this->published,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function scopes(){
		return array(
			'published' => array(
				'condition' => array(
					'published=1',
				)
			)
		);
	}

	public function beforeValidate(){
		$this->created = new CDbExpression('NOW()');
		if($this->isNewRecord) {
			$this->published = 0;
		}
		return true;
	}
}