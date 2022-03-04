<?php
/**
 * Created by JetBrains PhpStorm.
 * User: AlexOk
 * Date: 07.05.11
 * Time: 22:09
 * To change this template use File | Settings | File Templates.
 */
 
class Events extends CWidget
{
    public $show_title = false;
    public $view = 'list';
    public $type = 'news';

    public function run()
    {
        if (Yii::app()->params['hide_news'] == true)
            return;

        $criteria = new CDbCriteria();
        $criteria->condition = 'publish = 1';
        $criteria->addColumnCondition(['type' => $this->type]);
        $criteria->order = 'created DESC';
        $criteria->limit = 4;

        $events = Event::model()->findAll($criteria);

        if (!$events)
            return;

        $menu_item = CmsMenu::getInstance()->getItem('event', 'all');

        $show_all = ($menu_item && $menu_item->hidden) ? true : false;

        $this->render($this->view, compact('events', 'show_all'));
    }
}
