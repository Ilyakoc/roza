<?php

/**
 * This is the model class for table "offer".
 *
 * The followings are the available columns in table 'offer':
 * @property integer $id
 * @property string $title
 * @property integer $sort
 * @property integer $product_id
 * @property float $price
 * @property Product $product
 * @property OfferEav[] $eavAttributes
 */
class Offer extends CActiveRecord
{
    const TAPE_PRICE = 50; // Цена ленты
    const TAPE_ID = 38; // ID атрибута ленты

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'offer';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, product_id', 'required'),
            ['price, sale_value, diameter, height', 'safe'],
			array('sort, product_id', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, sort, product_id', 'safe', 'on'=>'search'),
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
		    'product' => [self::BELONGS_TO, 'Product', 'product_id'],
            'eavAttributes' => array(self::HAS_MANY, 'OfferEav', 'offer_id', 'index' => 'attribute_id', 'order' => 'eavAttribute.sort, eavAttribute.name', 'with' => 'eavAttribute', 'together' => true),
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
			'sort' => 'Порядок сортировки',
			'product_id' => 'Продукт',
            'price' => 'Фиксированная цена',

            'diameter' => 'Диаметр букета',
            'height' => 'Высота букета',
            'sale_value' => 'Размер скидки, %',
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
		$criteria->compare('sort',$this->sort);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('price',$this->price);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * @param $attributeID int
     * @param null $default
     * @return null|string
     */
	public function getEavAttributeValue($attributeID, $default = null)
    {
        $attributes = $this->eavAttributes;

        return isset($attributes[$attributeID]) ? $attributes[$attributeID]->value : $default;
    }

    protected function afterFind()
    {
        parent::afterFind();

        return true;
    }

    public function getPriceFromAttributes()
    {
        $newPrice = 0;

        $i = 0;

        foreach ($this->eavAttributes as $attribute) {
            $attributeModel = $attribute->eavAttribute;

            if (!$attributeModel) {
                continue;
            }
//            $newPrice += $attribute['value'] * $attribute->eavAttribute->relationPrice->price;
            $newPrice += $attribute['value'] * $attributeModel->getPriceByCount($attribute->value);

            $i++;
        }

        reset($this->eavAttributes);

        if ($i === 1) {
            $firstAttribute = current($this->eavAttributes);

            if ($firstAttribute->value != 1) {
                $newPrice += self::TAPE_PRICE;
            }
        }

        return $newPrice;
    }

    protected function afterSave() {
        parent::afterSave();

        $this->saveEavAttributes();
    }

    protected function saveEavAttributes()
    {
        if (Yii::app()->request->getPost('save_offer_attributes')) {
            $eavAttributes = Yii::app()->request->getPost('OfferEav', []);

            if (!$eavAttributes) {
                $eavAttributes = [];
            }

            OfferEav::model()->deleteAll('offer_id = :offer_id', [
                ':offer_id' => $this->id,
            ]);

            foreach ($eavAttributes as $key => $value) {
                if (!$value) {
                    continue;
                }

                $attributesProduct = new OfferEav;
                $attributesProduct->attribute_id = $key;
                $attributesProduct->offer_id = $this->id;

                $attributesProduct->value = $value;
                $attributesProduct->save();
            }
        }
    }

    /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Offer|CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
