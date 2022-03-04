<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 24.10.11
 * Time: 17:24
 * To change this template use File | Settings | File Templates.
 */
 
class CmsCart
{
    private $items    = array();
    private $products = array();

    public $isFirstProduct = false;
    public $isFirstItem  = false;

    public static $instance = null;

    /**
     * @static
     * @return CmsCart
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct()
    {
        $state = Yii::app()->user->getState('cart_items');

        if ($state)
            $this->items = $state;
    }

    private function saveState()
    {
        Yii::app()->user->setState('cart_items', $this->items);
    }

    public function generateHash($id, $data = [])
    {
        $str = $id;

        if (!empty($data['offer_id'])) {
            $str .= '_' . $data['offer_id'];    
        }

        return substr(sha1($str), 0, 6);
    }

    public function add($id, $count = 1, $data = [])
    {
        $hash = $this->generateHash($id, $data);

        if (count($this->items) == 0) {
            $this->isFirstProduct = true;
        }

        if (isset($this->items[$hash])) {
            $this->items[$hash]['count'] = $this->items[$hash]['count'] + $count;
        } else {
            $this->items[$hash]['count'] = $count;
            $this->items[$hash]['id'] = $id;

            if (!empty($data['offer_id'])) {
                $this->items[$hash]['attributes']['offer_id'] = $data['offer_id']; 
            }

            $this->isFirstItem = true;
        }
        $this->saveState();
    }

    public function update($hash, $count)
    {
        if (isset($this->items[$hash])) {
            if ($count > 0) {
                $this->items[$hash]['count'] = $count;
            } else {
                unset($this->items[$hash]);
            }
            $this->saveState();
        }
    }

    public function clear()
    {
        $this->items = array();
        $this->saveState();
    }

    public function count($hash)
    {
        return isset($this->items[$hash]) ? $this->items[$hash]['count'] : 0;
    }

    public function countAll()
    {
        $count = 0;
        foreach($this->items as $data)
            $count += $data['count'];
        
        return $count;
    }

    public function countAllProducts()
    {
        return count($this->items);
    }
  
    public function priceProducts()
    {
        return count($this->items);
    }

    public function priceAll()
    {
        $products = $this->getProducts();
        
        $value = 0;
        foreach($products as $hash => $product) {
            $value += $product->price * $this->items[$hash]['count'];
        }

        return $value;
    }

    /**
     * Return summary data of Cart
     * @return stdClass
     */
    public function cartInfo()
    {
        $result = new stdClass();
        $result->summary_count = $this->countAll();
        $result->summary_price = $this->priceAll();
        $result->products      = $this->getProducts();
        $result->self          = $this;
        
        return $result;
    }

    public function getHtmlSummary()
    {
        ob_start();
        Yii::app()->controller->widget('widget.ShopCart.ShopCart', array('summary'=>true));
        $content = ob_get_contents();
        ob_clean();
        
        return $content;
    }

    public function getHtmlProducts()
    {
        ob_start();
        Yii::app()->controller->widget('widget.ShopCart.ShopCart', array('products'=>true));
        $content = ob_get_contents();
        ob_clean();

        return $content;
    }

    public function getJsonResult()
    {
        $products = $this->getProducts();
        $result   = array();

        foreach($products as $hash=>$product) {
            $result[$hash] = array(
                'id'=>$product->id,
                'order_price'=>$product->price,
                'offer_title'=>$product->offer_title,
                'count'=>$this->count($hash)
            );
        }

        return json_encode($result);
    }

    public function getResult($full = false)
    {
        $products = $this->getProducts();

        $result   = array();

        foreach($products as $hash=>$product) {

            $result[$hash] = (object) array(
                'id'=>$product->id,
                'order_price'=>$product->price,
                'count'=>$this->count($hash),
                'title'=>$product->title,
                'obj'=>$full ? $product : null
            );
        }

        return $result;
    }

    /**
     * Return products list from cache array and base
     * @param array $ids
     * @return array
     */
    private function getProducts($ids = array())
    {
        if (!$this->products) {

            foreach ($this->items as $item) {
                $ids[] = $item['id'];
            }

            $products = Product::model()->with('category')->together()->findAllByPk($ids, array('index'=>'id'));

            $items = [];

            foreach ($this->items as $hash => $item) {
                $product = clone $products[$item['id']];

                if (!empty($item['attributes']['offer_id'])) {
                    $offer = \Offer::model()->findByPk($item['attributes']['offer_id']);

                    $product->offer_title = $offer->title;
                    $product->price = (float) $offer->price;
                }

                $items[$hash] = $product;
            }

            $this->products = $items;
        }

        return $this->products;
    }
}
