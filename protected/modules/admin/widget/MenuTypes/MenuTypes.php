<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 16.01.12
 * Time: 12:42
 * To change this template use File | Settings | File Templates.
 */
class MenuTypes extends CWidget
{
    public function run()
    {
        $types = array(
            'page'=>'страницы',
            'link'=>'ссылки',
            'blog'=>'блога'
        );

        $this->render('default', compact('types'));
    }
}
