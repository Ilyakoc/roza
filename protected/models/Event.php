<?php

/**
 * This is the model class for table "Event".
 *
 * The followings are the available columns in table 'Event':
 * @property integer $id
 * @property string $title
 * @property string $text
 * @property string $created
 * @property integer $publish
 */
class Event extends DActiveRecord
{
    public $image;
    public $file;
	protected $oneImage;
	/**
     * @static
     * @param string $className
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
		return 'event';
	}
	
	public function behaviors()
	{
		return [
			'AliasBehavior'=>'DAliasBehavior'
		];
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return parent::rules(array(
			array('title, text, created, type', 'required'),
			array('publish', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, text, created, publish, type', 'safe', 'on'=>'search'),
		));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return parent::relations(array(
		));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'ID',
			'title' => 'Заголовок',
			'text' => 'Текст новости',
			'created' => 'Создана',
			'publish' => 'Активно?',
			'type' => 'Тип материала',
		));
	}

    protected function getDate()
    {
        return Yii::app()->dateFormatter->format('dd.MM.yyyy', $this->created);
    }

    /**
     * Get first paragraph of content
     *
     * @return string
     */
 /*   public function getIntro()
    {
        preg_match('%<p[^>]*>(.*)</p>%', $this->text, $array);
        $txt = '<p>'. $array[1]. '</p>';
        return $txt;
    }
*/
    protected function beforeValidate()
    {
        $this->image = CUploadedFile::getInstances($this, 'image');
        $this->file  = CUploadedFile::getInstances($this, 'file');
        return true;
    }

    protected function afterSave()
    {
        $upload = new UploadHelper;

        if (count($this->image))
            $upload->add($this->image, $this);

        if (count($this->file))
            $upload->add($this->file, $this, 'file');

        $upload->runUpload();
    }

    protected function afterDelete()
    {
        $params = array(
            'model'   => strtolower(get_class($this)),
            'item_id' => $this->id
        );

        $items = array_merge(
            CImage::model()->findAllByAttributes($params),
            File::model()->findAllByAttributes($params)
        );

        foreach($items as $item)
            $item->delete();

        return true;
    }
    
    
    
    public function getOneImage()
    {
        if ($this->oneImage == null) {
            $this->oneImage = CImage::model()->findAll('model=? AND item_id=?', array(
                strtolower(get_class($this)),
                $this->id
            ));
        }
		if(isset($this->oneImage[0]))
			return '/images/event/' . $this->oneImage[0]->filename;
		else
			return '';
		
    }
    
    
    
    
}
