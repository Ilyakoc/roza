<?php
/**
 * Created by JetBrains PhpStorm.
 * User: AlexOk
 * Date: 07.05.11
 * Time: 11:14
 */

class coreHelper
{
    public static function generateHash($length = 8)
    {
        $hash = '';
        $arr = array('a','b','c','d','e','f',
                     'g','h','i','j','k','l',
                     'm','n','o','p','r','s',
                     't','u','v','x','y','z',

                     'A','B','C','D','E','F',
                     'G','H','I','J','K','L',
                     'M','N','O','P','R','S',
                     'T','U','V','X','Y','Z',

                     '1','2','3','4','5','6',
                     '7','8','9','0'
                     );

        $max = count($arr) - 1;

        for($i = 0; $i < $length; $i++) {
            $index = rand(0, $max);
            $hash .= $arr[$index];
        }

        return $hash;
    }

    public static function getMenuItems()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'publish = 1';
        $criteria->order = 'id';

        $pages = Page::model()->findAll($criteria);
        $items = array();

        foreach($pages as $i=>$page) {
            if ($i == 2) {
                //$items[] = array('label'=>'События', 'url'=>array('/site/news'));
            }

            if ($page->mainpage != 1) {
                $items[] = array('label'=>$page->title, 'url'=>array('/site/page', 'alias'=>$page->alias));
            }
        }

        //$items[] = array('label'=>'Контакты', 'url'=>array('/site/contact'));
        return $items;
    }

    public static function getAdminMenuItems()
    {
        $pages = Page::model()->findAll('publish = 1');
        $items = array();

        foreach($pages as $i=>$page) {
            if ($i == 2) {
                $items[] = array('label'=>'События', 'url'=>array('event/index'));
            }

            if ($page->mainpage != 1) {
                $items[] = array('label'=>$page->title, 'url'=>array('page/update', 'id'=>$page->id));
            }
        }

        //$items[] = array('label'=>'Контакты', 'url'=>array('/site/contact'));
        return $items;
    }

    public static function getNotifies($modelName) {
        if($modelName == "order") {
            return $count = Order::model()->notcompleted()->count();
        }
        if($modelName == "question") {
            return $count = Question::model()->unanswered()->count();
        }
    }
}
 
