<?php
use common\components\helpers\HArray as A;

/**
 * This is the model class for table "page".
 *
 * The followings are the available columns in table 'page':
 * @property integer $id
 * @property integer $blog_id
 * @property string $alias
 * @property string $title
 * @property string $intro
 * @property string $text
 * @property string $created
 * @property string $modified
 *
 * @property Metadata $meta[]
 */
class Page extends \common\components\base\ActiveRecord
{
    public $image;
    public $file;

    public $meta_title;
    public $meta_key;
    public $meta_desc;

	/**
	 * Returns the static model of the specified AR class.
     * @param mixed
	 * @return Page the static model class
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
		return 'page';
	}

    public function behaviors()
    {
        return A::m(parent::behaviors(), [
            'updateTimeBehavior'=>[
                'class'=>'\common\ext\updateTime\behaviors\UpdateTimeBehavior',
                'addColumn'=>false
            ],
        ]);
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return $this->getRules(array(
			array('alias, title, text', 'required'),
            array('blog_id', 'numerical', 'integerOnly'=>true),
            array('alias', 'unique'),
            array('alias', 'urlPath'),
            array('meta_title, meta_key, meta_desc', 'safe'),
			array('alias, title', 'length', 'max'=>255),
            array('created, modified', 'unsafe')
		));
	}

    public function urlPath($attribute, $params = null) {
        $pattern = '/^[-\w\d]+$/ui';
        if(!preg_match($pattern, $this->$attribute)) {
            $this->addError($attribute, $this->getAttributeLabel($attribute).' может содержать только буквы, цифры и символы "-"');
        }
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return $this->getRelations(array(
            'blog'=>array(self::BELONGS_TO, 'Blog', 'blog_id'),
            'meta'=>array(self::BELONGS_TO, 'Metadata', array('id'=>'owner_id'),
                'together'=>true,
                'condition'=>'owner_name = :owner_name',
                'params'=>array(':owner_name'=>strtolower(get_class($this)))
            )
		));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels(array(
			'id' => 'ID',
            'blog_id' => 'Блог',
			'alias' => 'Url',
			'title' => 'Заголовок',
			'intro' => 'Вводный текст',
			'text' => 'Текст',
			'created' => 'Создана',
            'modified' => 'Изменена',
            'meta_title'=>'Заголовок',
            'meta_key'=>'Ключевые слова',
            'meta_desc'=>'Описание',
		));
	}

    public function getIntro()
    {
        preg_match('%<p[^>]*>(.*)</p>%', $this->text, $array);
        $txt = '<p>'. $array[1]. '</p>';

        ContentDecorator::decorate($this);
        return $txt;
    }

    protected function getDate()
    {
        return Yii::app()->dateFormatter->format('dd.MM.yyyy', $this->created);
    }

    public function isDefault()
    {
        $menuItem = CmsMenu::getInstance()->getItem($this);

        if (!$menuItem)
            return false;

        if (!$menuItem->default)
            return false;

        return true;
    }

    protected function afterFind()
    {
        //$format = 'dd.MM.yyyy HH:mm';
        //$this->created  = Yii::app()->dateFormatter->format($format, $this->created);
        //$this->modified = Yii::app()->dateFormatter->format($format, $this->modified);

        if ($this->meta)
            $this->attributes = $this->meta->attributes;

        return true;
    }

    protected function beforeValidate()
    {
        $this->alias = trim($this->alias);
        $this->image = CUploadedFile::getInstances($this, 'image');
        $this->file  = CUploadedFile::getInstances($this, 'file');

        if ($this->isNewRecord) {
            $this->created = new CDbExpression('NOW()');
        } else {
            $this->modified = new CDbExpression('NOW()');
        }

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

        if (!$this->blog_id) {
            // Update site menu
            if ($this->isNewRecord)
                CmsMenu::getInstance()->addItem($this);
            else
                CmsMenu::getInstance()->updateItem($this);
        }

        if (!$this->meta) {
            $this->meta = new Metadata();
            $this->meta->owner_name = get_class($this);
            $this->meta->owner_id   = $this->id;
        }

        $this->meta->attributes = $this->getAttributes(array('meta_title', 'meta_key', 'meta_desc'));
        $this->meta->save();
        
        return true;
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

        CmsMenu::getInstance()->removeItem($this);

        return true;
    }
}
