<?php
use common\components\helpers\HArray as A;

/**
 * This is the model class for table "category".
 *
 * The followings are the available columns in table 'category':
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $ordering
 */
class Category extends \common\components\base\ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Category the static model class
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
		return 'category';
	}

    public function behaviors()
    {
        return A::m(parent::behaviors(), [
        	'MetaBehavior'=>'MetadataBehavior',
        	'AliasBehavior'=>'DAliasBehavior',
            'NestedSetBehavior'=>array(
                'class'=>'ext.yiiext.behaviors.trees.NestedSetBehavior',
                'leftAttribute'=>'lft',
                'rightAttribute'=>'rgt',
                'levelAttribute'=>'level',
                'hasManyRoots'=>true
            ),
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
			array('title', 'required'),
			['hide_menu', 'safe'],
			//array('ordering', 'numerical', 'integerOnly'=>true),
			array('title, view_template', 'length', 'max'=>255),
			array('meta_desc', 'length', 'max'=>255),
			array('description, parent_id, under_description, front, meta_desc, sort_slider', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, description, ordering, front, under_description', 'safe', 'on'=>'search'),
		));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return $this->getRelations(array(
            'tovars'=>array(self::HAS_MANY, 'Product', 'category_id')
		));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels(array(
			'id' => 'ID',
			'title' => 'Название',
			'description' => 'Описание',
			'under_description' => 'Описание в конце всех товаров',
			'ordering' => 'Порядок',
			'front' => 'Сделать слайдер на главной',
            'parent_id'=>'Родитель',
			'meta_desc'=>'meta_desc',
			'view_template'=>'Шаблон отображения',
			'hide_menu' => 'Скрыть из меню',
			'sort_slider' => 'Порядок сортировки слайдера',
		));
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('ordering',$this->ordering);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
