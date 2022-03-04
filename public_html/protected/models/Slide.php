<?php

/**
 * This is the model class for table "slide".
 *
 * The followings are the available columns in table 'slide':
 * @property integer $id
 * @property string $title
 * @property string $link
 * @property string $filename
 * @property integer $ordering
 *
 * @property CUploadedFile $file
 */
class Slide extends CActiveRecord
{
    public $file;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Slide the static model class
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
		return 'slide';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title', 'required'),
			['hide_title', 'safe'],
			array('type', 'in', 'range' => array(1,2)),
			array('title, link', 'length', 'max'=>255),
            array('file', 'file', 'allowEmpty'=>true)
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
			'title' => 'Заголовок',
			'type' => 'Тип',
			'file' => 'Изображение',
            'link' => 'Ссылка',
			'ordering' => 'Ordering',
			'hide_title' => 'Не выводить заголовок',
		);
	}

    public function getSrc()
    {
        $file = Yii::getPathOfAlias('webroot.images.carousel').DS.$this->filename;
        if (is_file($file)) {
            return '/images/carousel/'.$this->filename;
        }
        return false;
    }

    protected function beforeValidate()
    {
        $this->file = CUploadedFile::getInstance($this, 'file');
        return true;
    }

    protected function beforeSave()
    {
        if ($this->file instanceof CUploadedFile) {
            $path = Yii::getPathOfAlias('webroot.images.carousel');

            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $name = coreHelper::generateHash().'.'.$this->file->extensionName;
            $this->file->saveAs($path .DS. $name);

            $image = Yii::app()->image->load($path.DS.$name);
            
            $carouselWidth = Yii::app()->params['slider']['carousel']['width'];
            $carouselHeight = Yii::app()->params['slider']['carousel']['height'];

            $slideshowWidth = Yii::app()->params['slider']['slideshow']['width'];
            $slideshowHeight = Yii::app()->params['slider']['slideshow']['height'];

            if($this->type != 2) { // !slideshow
	            if ($image->width > $carouselWidth || $image->height > $carouselHeight) {
	                $image->resize($carouselWidth, $carouselHeight, Image::HEIGHT)
	                	  ->crop($carouselWidth, $carouselHeight)
	                	  ->save();
	            }
        	} else {
        		if ($image->width > $slideshowWidth || $image->height > $slideshowHeight) {
	                $image->resize($slideshowWidth, $slideshowHeight, Image::HEIGHT)
	                	  ->crop($slideshowWidth, $slideshowHeight)
	                	  ->save();
	            }
        	}

            $this->filename = $name;
        }
        return true;
    }
}
