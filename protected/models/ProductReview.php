<?php

/**
 * This is the model class for table "product_review".
 *
 * The followings are the available columns in table 'product_review':
 * @property integer $id
 * @property string $username
 * @property string $text
 * @property string $ts
 * @property integer $ip
 * @property integer $published
 */
class ProductReview extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProductReview the static model class
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
		return 'product_review';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, product_id, text, mark', 'required'),
			array('ip, mark', 'numerical', 'integerOnly'=>true),
			array('username', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, username, text, ts, ip, published', 'safe', 'on'=>'search'),
		);
	}

    public function scopes() {
        return array(
            'published' => array(
                'condition' => 'published=1',
            )
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
            'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
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
            'product_id' => 'Товар',
            'mark' => 'Ваша оценка',
			'text' => 'Текст',
			'ts' => 'Время',
			'ip' => 'IP',
			'published' => 'Опубликовано',
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
		$criteria->compare('text',$this->text,true);
		$criteria->compare('ts',$this->ts,true);
		$criteria->compare('ip',$this->ip);
		$criteria->compare('published',$this->published);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    protected function beforeValidate(){
        if($this->isNewRecord) {
            $this->ts = new CDbExpression("NOW()");
            $this->ip = ip2long($_SERVER['REMOTE_ADDR']);
            $this->published = 0;
        }
        return true;
    }
}