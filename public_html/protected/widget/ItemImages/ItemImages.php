<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alexok
 * Date: 20.05.11
 * Time: 18:12
 * To change this template use File | Settings | File Templates.
 */

class ItemImages extends CWidget
{
    public $model;
    public $item_id;
    public $view = 'site';
    public $countPerPage;

    public function run()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'model = :model AND item_id = :item_id';
        $criteria->params['model']   = $this->model;
        $criteria->params['item_id'] = $this->item_id;
        $criteria->order = 'ordering';

        $images = CImage::model()->findAll($criteria);

        if ($this->countPerPage == null) {
            $this->countPerPage = isset(Yii::app()->params['count_per_page']) ? Yii::app()->params['count_per_page'] : 21;
        }

        if ($images) {
            $pages = ceil(count($images) / $this->countPerPage);
            $this->render($this->view, compact('images', 'pages'));
        }
    }
}
