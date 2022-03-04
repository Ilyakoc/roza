<?php

/**
 * This is the model class for table "metadata".
 *
 * The followings are the available columns in table 'metadata':
 * @property integer $id
 * @property string $owner_name
 * @property integer $owner_id
 * @property string $meta_title
 * @property string $meta_key
 * @property string $meta_desc
 */
class Metadata extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Metadata the static model class
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
		return 'metadata';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('owner_id', 'numerical', 'integerOnly'=>true),
			array('owner_name', 'length', 'max'=>50),
			array('meta_title', 'length', 'max'=>255),
            array('owner_name, owner_id', 'unsafe'),
            array('meta_title, meta_key, meta_desc, meta_h1', 'safe')
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
			'owner_name' => 'Owner Name',
			'owner_id' => 'Owner',
			'meta_title' => 'Meta Title',
			'meta_key' => 'Meta Key',
			'meta_desc' => 'Meta Desc',
			'meta_h1'=>'H1', 
		);
	}

    protected function beforeSave()
    {
        $this->owner_name = strtolower($this->owner_name);
        return true;
    }

    public function getTitle()
    {
        return $this->meta_title;
    }

    public function getKey()
    {
        return $this->meta_key;
    }

    public function getDesc()
    {
        return $this->meta_desc;
    }

    public function getH1()
    {
    	return $this->meta_h1;
    }    
}
