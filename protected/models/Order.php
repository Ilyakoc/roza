<?php

/**
 * This is the model class for table "order".
 *
 * The followings are the available columns in table 'order':
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property string $comment
 * @property string $products
 * @property string $created
 * @property string|int $payment
 * @property boolean $payment_complete
 *
 * @property int $summaryPrice
 */
class Order extends CActiveRecord
{
    public $self_export;

    private $_payment;
    private $_delivery;


	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
	   /*return [
           ['area', 'required', 'when' => function ($model) { return $model->delivery == 2; }, 'whenClient' => "function (attribute, value) { return $('#id').val() == '2'; }"],
       ];*/
		return array(
            array('name, email, phone, personal', 'required'),
//			array('card_text', 'safe'),
          //  array('address', 'required', 'on'=>''),
		   // array('area', 'required', 'when' => function ($this) {return TRUE;}),
		   //['area', 'required', 'when' => function ($model) { return $model->delivery == 2; }, 'whenClient' => "function (attribute, value) { return $('#id').val() == '2'; }"],
		   //['area', 'required', 'message' => 'Please choose a username.'],
            array('completed, delivery, area, wish_text', 'safe'),
			array('name', 'length', 'max'=>50),
			array('email, phone, address, comment', 'length', 'max'=>255),
            array('payment', 'numerical', 'integerOnly'=>true, 'on'=>'payment'),
            ['delivery_price, notice, recipient_name, recipient_phone, recipient_date, time, address', 'safe'],
          //  array('delivery', 'numerical', 'integerOnly'=>true)
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
			'name' => 'Отправитель: Имя',
			'email' => 'Отправитель: Email',
			'phone' => 'Отправитель: Телефон',
			'personal' => 'Cогласие на обработку персональных данных',

            'recipient_name' => 'Получатель: Имя',
            'recipient_date' => 'Получатель: Дата доставки',
            'recipient_phone' => 'Получатель: Телефон',
            'time' => 'Получатель: Время доставки',

			'address' => 'Адрес доставки',
			'comment' => 'Комментарий к заказу',
			'products' => 'Товары',
            'created' => 'Дата заказа',
            'payment'=>'Способы оплаты',
            'delivery'=>'Способы доставки',
            'delivery_price'=>'Стоимость доставки',
            'area'=>'Район',
            'card_text' => 'Текст на открытке',
            'wish_text' => 'Пожелания к заказу',
            'notice' => 'Дополнительно',
		);
	}

    /*protected function beforeValidate()
    {
        $this->products = CmsCart::getInstance()->getJsonResult();
        return parent::beforeValidate();
    }*/

    protected function beforeSave()
    {
        if ($this->isNewRecord) {
            $this->products = CmsCart::getInstance()->getJsonResult();
            $this->created = new CDbExpression('NOW()');
        }

        if ($this->scenario=='payment') {
            $payments = $this->getPaymentTypes();

            if (isset($payments[$this->payment])) {
                $this->_payment = $this->payment;
                $this->payment = $payments[$this->payment];
            }
        }

        return parent::beforeSave();
    }

    public function getProducts()
    {
        $products = json_decode($this->products);

        if (count($products)) {
            foreach($products as $id=>$p) {
                $model = Product::model()->findByPk($p->id);
                if ($model) {
                    $products->{$id}->title = $model->title;
                    $products->{$id}->code  = $model->code;
                } else {
                    $products->{$id}->title = $products->{$id}->code = 'Товар удален';
                }
            }

            return $products;
        } else {
            return array();
        }
    }

    public function getSummaryPrice()
    {
        $result = 0;

        $products = $this->getProducts();

        foreach($products as $p)
            $result += $p->order_price * $p->count;

        $result = $result + $this->delivery_price;

        return $result;
    }

    public function getDate()
    {
        return is_string($this->created) ? Yii::app()->dateFormatter->format('dd.MM.yyyy HH:mm',  $this->created) : date('d.m.Y H:i');
    }

    public function getPaymentTypes()
    {
        $items = array();

        if ($this->scenario=='payment') {
            foreach(Yii::app()->params['shopPayment']['types'] as $index=>$title) {
                $items[$index] = $title;
            }
        }

        return $items;
    }

	public function getDeliveryTypes()
    {
        $items = array();
        foreach(Yii::app()->params['deliveryTypes'] as $index=>$title) {
                $items[$index] = $title;
            }

        return $items;
    }
    
    public function getPaymentAction()
    {
        if ($this->_payment) {
            if (isset(Yii::app()->params['shopPayment']['actions'][$this->_payment])) {
                return Yii::app()->params['shopPayment']['actions'][$this->_payment];
            }
        }

        return false;
    }

	 
    public function getDelivery()
    {
        if ($this->delivery) {
            if (isset(Yii::app()->params['deliveryTypes'][$this->delivery])) {
                return Yii::app()->params['deliveryTypes'][$this->delivery];
            }
        }

        return false;
    }
    
    public function checkPayment()
    {
        if (isset(Yii::app()->params['shopPayment']) && Yii::app()->params['shopPayment']['actions']) {
            $this->setScenario('payment');
        }
    }

    public function scopes(){
        return array(
          'notcompleted' => array(
              'condition' => 'completed = 0'
          )
        );
    }
}
