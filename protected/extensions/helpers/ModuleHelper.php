<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alexok
 * Date: 01.06.11
 * Time: 18:29
 */
 
class ModuleHelper
{
    /**
     * Print or return the site param value
     * @static
     * @param $name
     * @param bool $return
     * @return mixed
     */
    public static function getParam($name, $return = false)
    {
        $value = Yii::app()->settings->get('cms_settings', $name);

        if ($return)
            return $value;
        else
            echo $value;
    }

    /**
     * Return or print the copyright website
     * @static
     * @param bool $return
     * @return string
     */
    public static function Copyright($return = false)
    {
        $text = '&copy; '; // echo date('Y');  ООО С-Авто-М

        $dev_year = Yii::app()->params['dev_year'];
        $cur_year = date('Y');

        if ($dev_year != $cur_year) {
            $dev_year .= '-'. $cur_year;
        }
        $text .= $dev_year .' '. ModuleHelper::getParam('firm_name', true);

        if ($return)
            return $text;
        else
            echo $text;
    }
}
