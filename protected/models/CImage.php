<?php

/**
 * This is the model class for table "image".
 *
 * The followings are the available columns in table 'image':
 * @property integer $id
 * @property string $model
 * @property integer $item_id
 * @property string $filename
 * @property string $description
 * @property integer $ordering
 */
class CImage extends CActiveRecord
{
	/**
     * @static
     * @param $className
     * @return CActiveRecord
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
		return 'image';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('model, item_id, filename', 'required'),
			array('item_id', 'numerical', 'integerOnly'=>true),
			array('model', 'length', 'max'=>20),
			array('filename', 'length', 'max'=>100),
			array('description', 'length', 'max'=>500),
            array('ordering', 'safe')
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
			'model' => 'Модель',
			'item_id'=>'Id записи',
			'filename'=>'Имя файла',
			'description'=>'Описание',
            'ordering'=>'Порядок'
		);
	}

    protected function afterDelete()
    {
        $path = YiiBase::getPathOfAlias('webroot').DS.'images'.DS.$this->model;

        $files = array($this->filename, 'tmb_'.$this->filename);

        foreach($files as $f) {
            if (!is_file($path .DS. $f) && defined('YII_DEBUG')) {
                throw new CException('Файл не найден');
            }

            if (is_file($path .DS. $f)) {
                unlink($path .DS. $f);
            }
        }

        return true;
    }

    public function getUrl()
    {
        return '/images/'.$this->model.'/'.$this->filename;
    }

    public function getTmbUrl()
    {
        $path = YiiBase::getPathOfAlias('webroot') .DS. 'images' .DS. $this->model;
        if (is_file($path .DS. $this->filename)) {

        }

        return '/images/'.$this->model.'/tmb_'.$this->filename;
    }

    public function removeTmb()
    {
        $file = YiiBase::getPathOfAlias('webroot') .DS. 'images' .DS. $this->model .DS. 'tmb_'.$this->filename;

        if (is_file($file))
            return unlink($file) ? true : false;

        return false;
    }

    public function getPath($full = true)
    {
        $base = YiiBase::getPathOfAlias('webroot') .DS. 'images';
        return $full ? $base .DS. $this->model : $base;
    }

    private function urlToPath($url) {
    	$path = $_SERVER['DOCUMENT_ROOT'].mb_strcut($url, 0, mb_strpos($url, '?'));
    	if(file_exists($path))
    		return $path;
    	else
    		return false;
    }

    public function getWidth($image) {
    	if(is_file($this->urlToPath($image))) {
	        $imageObject = Yii::app()->image->load($this->urlToPath($image));
	        return $imageObject->width;
    	}
    }

    public function getHeight($image) {
    	if(is_file($this->urlToPath($image))) {
	        $imageObject = Yii::app()->image->load($this->urlToPath($image));
	        return $imageObject->height;
    	}
    }

}
