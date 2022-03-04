<?php

/**
 * This is the model class for table "big_button".
 *
 * The followings are the available columns in table 'big_button':
 * @property integer $id
 * @property string $title
 * @property string $preview
 * @property string $link
 * @property integer $sort
 * @property integer $active
 * @property string $alt
 */
class BigButton extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'big_button';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, link', 'required'),
			['preview, alt, active', 'safe'],
			array('sort, active', 'numerical', 'integerOnly'=>true),
			array('title, preview, link, alt', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, preview, link, sort, active, alt', 'safe', 'on'=>'search'),
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
			'title' => 'Название',
			'preview' => 'Изображение',
			'link' => 'Ссылка',
			'sort' => 'Порядок сортировки',
			'active' => 'Активность',
			'alt' => 'Alt изображения',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('preview',$this->preview,true);
		$criteria->compare('link',$this->link,true);
		$criteria->compare('sort',$this->sort);
		$criteria->compare('active',$this->active);
		$criteria->compare('alt',$this->alt,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BigButton the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
