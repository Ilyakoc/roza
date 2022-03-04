<?php
use common\components\helpers\HArray as A;

/**
 * This is the model class for table "product".
 *
 * The followings are the available columns in table 'product':
 * @property integer $id
 * @property integer $category_id
 * @property string $code
 * @property string $title
 * @property string $description
 * @property integer $price
 * @property boolean $notexist
 * @property boolean $new
 * @property integer $ordering
 * @property CUploadedFile $mainImg
 * @property CUploadedFile $moreImg
 * @property string $path Get path for images directory
 *
 * @property string|boolean ext
 */
class Product extends \common\components\base\ActiveRecord
{
    public $property;
    public $offer_title = '';
    public $setRelatedProducts = false;

    protected $mainImg;
    protected $moreImg;

    protected $sizes = array(
        'full'=>array(
            'suffix'=>'',
            'size'=>900,
            'masterSize'=>4
        ),
        'big'=>array(
            'suffix'=>'_b',
            'size'=>320,
            'masterSize'=>4
        ),
        'small'=>array(
            'suffix'=>'_s',
            'size'=>280,
            'crop'=>1
        ),
        'tmb'=>array(
            'suffix'=>'_tmb',
            'size'=>118,
            'crop'=>1
        )
    );

    protected $exts = array('jpg', 'png', 'gif');

	/**
	 * Returns the static model of the specified AR class.
	 * @return Product the static model class
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
		return 'product';
	}
	
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
			'MetaBehavior'=>'MetadataBehavior',
			'AliasBehavior'=>'DAliasBehavior',
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
			array('category_id, title, price', 'required'),
			array('category_id, price, old_price, ordering', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			array('size', 'length', 'max'=>50),
			array('weight', 'length', 'max'=>50),
            array('mainImg', 'file', 'allowEmpty'=>true, 'types'=>'jpg, gif, png'),
            array('notexist, sale, new', 'boolean'),
            array('description, moreImg, code', 'safe'),
			array('diameter, height, sale_value, composition, setRelatedProducts, offer_title', 'safe'),
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
            'category'=>array(self::BELONGS_TO, 'Category', 'category_id'),
            'reviews'=>array(self::HAS_MANY, 'ProductReview', 'product_id'),
			'relatedCategories'=>array(self::HAS_MANY, 'RelatedCategory', 'product_id'),
            'relatedProducts'=>array(self::HAS_MANY, 'Related', 'product_id', 'index' => 'related_id', 'order'=>'id'),
            'productAttributes'=>array(self::HAS_MANY, 'EavValue', 'id_product'),
            'offers' => [self::HAS_MANY, 'Offer', 'product_id', 'order' => 'offers.sort'],
            'offersByPrice' => [self::HAS_MANY, 'Offer', 'product_id', 'order' => 'offersByPrice.price'],
		));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels(array(
			'id' => 'ID',
			'category_id' => 'Категория',
			'title' => 'Название',
            'code'=>'Артикул',
			'description' => 'Описание',
            'price' => 'Цена',
			'old_price' => 'Старая цена',
            'ordering'=> 'Порядок',
            'property'=>'Свойство',
            'mainImg' => 'Главное фото',
            'moreImg' => 'Дополнительные фото',
            'notexist'=>'Нет в наличии',
            'sale'=>'Спецпредложение',
            'new'=>'Новинка',
            'size'=>'Размер',
            'weight'=>'Вес',

            'diameter' => 'Диаметр букета',
            'height' => 'Высота букета',
            'sale_value' => 'Размер скидки, %',
            'composition' => 'Состав',
		));
	}
	
	/**
	 * ВАЖНО! использовать "OR" при $criteria->mergeWith($this->getRelatedCriteria(), 'OR');
	 *
	 * Получить объект критерия выборки для связанных товаров.
	 * Через Scope реализовать нет возможности, т.к. критерий должен быть объединен
	 * к выражению выборки товаров, как OR. Следовательно, в зависимости от конекста.
	 * @param integer|array|NULL $categoryId id категории, или массив идентификаторов.
	 * По умолчанию NULL ($this->category_id)
	 * @param string $tableAlias алиас основной таблицы товаров в выборке.
	 * По умолчанию "`t`".
	 * @return \CDbCriteria
	 */
	public function getRelatedCriteria($categoryId=null, $tableAlias='`t`')
	{
		$criteria=new CDbCriteria;
		$criteria->addCondition("{$tableAlias}.`id`=`related_category`.`product_id`");
		$criteria->join.=' LEFT JOIN `related_category` ON (`related_category`.`category_id`';
		if(is_array($categoryId)) {
			$criteria->join.=' IN ('.implode(',', array_map(function($id) { return (int)$id; }, $categoryId)).')';
		}
		else {
			$criteria->join.='=:_rcCategoryId';
			$criteria->params[':_rcCategoryId']=($categoryId !== null) ? (int)$categoryId : $this->category_id;
		}
		$criteria->join.=')';
		$criteria->group=$tableAlias.'.`id`';
		
		return $criteria;
	}

    public function getCategories()
    {
        $cats_list = Category::model()->findAll(array('order'=>'root, lft'));;
        if (isset(Yii::app()->params['subcategories'])) {
            $cats_list = CmsCore::prepareTreeSelect($cats_list);
        }
        $categories = CHtml::listData($cats_list, 'id', 'title');
        return $categories;
    }

    protected function beforeValidate()
    {
        $this->mainImg = CUploadedFile::getInstance($this, 'mainImg');
        $this->moreImg = CUploadedFile::getInstances($this, 'moreImg');

        return true;
    }

    protected function afterSave()
    {
    	parent::afterSave();
    	
        if ($this->mainImg instanceof CUploadedFile) {
            $this->createMainImages();
        }

        if (count($this->moreImg)) {
            $this->createMoreImages();
        }

        if ($this->setRelatedProducts) {
            $this->saveRelatedProducts();
        }

        return true;
    }

    public function saveRelatedProducts()
    {
        Related::model()->deleteAll('product_id = :product_id', [
            ':product_id' => $this->id,
        ]);

        $related = Yii::app()->request->getPost('related', []);

        if (!$related) {
            $related = [];
        }

        foreach ($related as $key => $value) {
            $relatedProduct = new Related;
            $relatedProduct->product_id = $this->id;
            $relatedProduct->related_id = $value;
            $relatedProduct->save();
        }
    }

    public function afterDelete()
    {
    	$this->removeMainImage();
        return true;
    }

    protected function afterFind()
    {
    	parent::afterFind();
    	
        $image = $this->id .'_s.' .$this->ext;

        if (is_file(Yii::getPathOfAlias('webroot.images.product') .DS. $image)) {
            $this->mainImg = '/images/product/'. $image;
        } else {
            $this->mainImg = '/images/shop/product_no_image.png';
        }
    }

    public function removeMainImage($id = null)
    {
        $id       = $id ? $id : $this->id;
        if (!$this->id) {
            $this->id = $id;
        }

        $path     = $this->path;
        $suffixes = array('', '_b', '_s', '_tmb');
        $ext = $this->ext;

        foreach($suffixes as $s) {
            $file = $path. DS . $id . $s .'.'. $ext;

            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function clearImageCache()
    {
        $suffixes = array('_b', '_s', '_tmb');
        $path     = $this->path;
        $files    = scandir($path);

        foreach($files as $file) {
            foreach($suffixes as $s) {
                if (strpos($file, $s) !== false)
                    unlink($path .DS. $file);
            }
        }
    }

    public function getMoreImages()
    {
        if ($this->moreImg == null) {
            $this->moreImg = CImage::model()->findAll('model=? AND item_id=?', array(
                strtolower(get_class($this)),
                $this->id
            ));
        }

        return $this->moreImg;
    }

    public function getMainImg($admin = false)
    {
        return $this->checkSize('small', $admin);
    }

    public function getBigMainImg($admin = false)
    {
        return $this->checkSize('big', $admin);
    }

    public function getTmbImg($admin = false)
    {
        return $this->checkSize('tmb', $admin);
    }

    public function getFullImg($bool = false)
    {
        $image = $this->id .'.' .$this->ext;

        if (is_file($this->path .DS. $image))
            return $bool ? true : '/images/product/' .$image .'?'.filemtime($this->path .DS. $image);
        else
            return $bool ? false : '/images/shop/product_no_image_b.png';
    }
  
  private function autorotate(Imagick $image)
    {
        switch ($image->getImageOrientation()) {
        case Imagick::ORIENTATION_TOPLEFT:
            break;
        case Imagick::ORIENTATION_TOPRIGHT:
            $image->flopImage();
            break;
        case Imagick::ORIENTATION_BOTTOMRIGHT:
            $image->rotateImage("#000", 180);
            break;
        case Imagick::ORIENTATION_BOTTOMLEFT:
            $image->flopImage();
            $image->rotateImage("#000", 180);
            break;
        case Imagick::ORIENTATION_LEFTTOP:
            $image->flopImage();
            $image->rotateImage("#000", -90);
            break;
        case Imagick::ORIENTATION_RIGHTTOP:
            $image->rotateImage("#000", 90);
            break;
        case Imagick::ORIENTATION_RIGHTBOTTOM:
            $image->flopImage();
            $image->rotateImage("#000", 90);
            break;
        case Imagick::ORIENTATION_LEFTBOTTOM:
            $image->rotateImage("#000", -90);
            break;
        default: // Invalid orientation
            break;
        }
        $image->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
        return $image;
    }

    private function createMainImages()
    {
        $path     = $this->path;
        $ext      = strtolower($this->mainImg->extensionName);
        $name     = $this->id. '.' .$ext;

        $this->mainImg->saveAs($path .DS. $name);
      
        // replace image rotate
        if ( class_exists("Imagick") ) {
          $imageSRC = $path .DS. $name;

          $img = new Imagick(realpath($imageSRC));

          $this->autorotate($img);
          $img->stripImage(); // if you want to get rid of all EXIF data
          $img->writeImage();
        }

        $this->checkSize('full', true, true, true);
        $this->checkSize('big', true, true);
        $this->checkSize('small', true, true);
        $this->checkSize('tmb', true, true);
    }

    private function createMoreImages()
    {
        $params = array('max'=>100, 'master_side'=>4);

        if ($cropTop = Yii::app()->settings->get('shop_settings', 'cropTop')) {
            $params['crop'] = true;
            $params['cropt_top'] = $cropTop;
        }

        $upload = new UploadHelper;
        $upload->add($this->moreImg, $this);
        $upload->runUpload($params);
    }

    protected function getPath()
    {
        return Yii::getPathOfAlias('webroot.images.product');
    }

    protected function getExt($name = null)
    {
        if (!$name) {
            $name = $this->id;
        }

        foreach($this->exts as $ext) {
            if (is_file($this->path .DS. $name .'.'. $ext)) {
                return $ext;
            }
        }

        return false;
    }

    /**
     * Return images link
     * @param string $sizeName Full name of size type
     * @param bool $admin
     * @param bool $createOnly
     * @param bool $force
     * @return string|bool
     * @throws CException
     */
    private function checkSize($sizeName, $admin, $createOnly = false, $force = false)
    {
        if (!isset($this->sizes[$sizeName])) {
            throw new CException('Size type not found');
        }

        $path    = $this->path;
        $params  = $this->sizes[$sizeName];
        $ext     = $this->ext;

        $fullImg = $this->id .'.'. $ext;
        $image   = $this->id . $params['suffix'] .'.'. $ext;

        if (!is_file($path .DS. $image) && is_file($path .DS. $fullImg) || $force) {
            $img = Yii::app()->image->load($path .DS. $fullImg);

            if (isset($params['masterSize'])) {
                $masterSize = $params['masterSize'];
            } else {
                $masterSize = $img->width > $img->height ? Image::HEIGHT : Image::WIDTH;
            }

            if ($img->width > $params['size']) {
                $img->resize($params['size'], $params['size'], $masterSize);

                $cropTop = Yii::app()->settings->get('shop_settings', 'cropTop');

                if (isset($params['crop']) && $cropTop) {
                    $img->crop($params['size'], $params['size'], $cropTop);
                }
            }

            $img->save($path .DS. $image);
        }

        if ($createOnly)
            return;

        if (is_file($path .DS. $image)) {
            return '/images/product/'. $image . '?'. filemtime($path .DS. $image);
        }

        return $admin ? false : '/images/shop/product_no_image'. $params['suffix'] .'.png';
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

    public static function getAllProducts($list = true, $select = 'id, title') {
        $command = Yii::app()->db->createCommand();
        $products = $command->select($select)->from('product')->queryAll();

        return $list ? CHtml::listData($products, 'id', function($data) {
            return '[' . $data['id'] . '] ' . $data['title'];
        }) : $products;
    }

}
