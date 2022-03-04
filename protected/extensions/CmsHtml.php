<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 30.09.11
 * Time: 19:11
 * To change this template use File | Settings | File Templates.
 */
 
class CmsHtml
{
    static $_state = array();

    public static function head()
    {
        self::jquery();
        self::css();
        self::metaTags();
        self::noskype();
    }

    public static function css($files = array())
    {
        if (!$files)
            $files = array('template.css', 'style.css');
            // $files = array('editor.css', 'shop.css', 'form.css', 'question.css', 'review.css', 'template.css', 'style.css');

        $cs = Yii::app()->clientScript;

        foreach($files as $file) {
            if ($path = self::getCssPath($file)) {
                $cs->registerCssFile($path.'/'.$file);
            }
        }
    }

    private static function getCssPath($file_name)
    {
        if (is_file(Yii::getPathOfAlias('webroot.css').DS.$file_name)) {
            return '/css';
        }

        $theme = Yii::app()->theme;
        if (is_file(Yii::getPathOfAlias('webroot.themes.'.$theme->name.'.css') .DS. $file_name)) {
            return $theme->baseUrl.'/css';
        }

        return false;
    }

    /**
     * Find all allowed css files
     *
     * @static
     * @return array
     */
    private static function findAllCss()
    {
        $paths = array(
            'css',
            'themes.'.Yii::app()->theme->name.'.css'
        );

        $exclude = array('.', '..');

        $result = array();

        foreach($paths as $path) {
            $files = scandir(Yii::getPathOfAlias('webroot.'.$path));
            foreach($files as $file) {
                if (in_array($file, $exclude))
                    continue;
                $result[] = $file;
            }
        }
        return $result;
    }

    public static function jquery()
    {
        Yii::app()->clientscript->registerCoreScript('jquery');
    }

    public static function js($src = '', $jquery = true)
    {
        if (empty($src))
            throw new CException('Не указан js-файл');

        if (is_array($src)) {
            foreach($src as $link)
                Yii::app()->clientScript->registerScriptFile($link, CClientScript::POS_END);
        } else
            Yii::app()->clientScript->registerScriptFile($src, CClientScript::POS_END);

    }

    public static function noskype()
    {
        Yii::app()->clientScript->registerMetaTag('SKYPE_TOOLBAR_PARSER_COMPATIBLE', 'SKYPE_TOOLBAR');
    }

    public static function fancybox()
    {
        if (isset(self::$_state['fancybox']))
            return;

        $cs = Yii::app()->clientScript;

        $cs->registerCoreScript('jquery');
//        $cs->registerScriptFile('/js/fancybox/jquery.fancybox-1.3.4.pack.js');
//        $cs->registerCssFile('/js/fancybox/jquery.fancybox-1.3.4.css');

        self::$_state['fancybox'] = true;
    }

    public static function metaTags()
    {
        echo "<title>". CHtml::encode(Yii::app()->controller->pageTitle)."</title>\n";

        $cs = Yii::app()->clientScript;

        $cs->registerMetaTag('text/html; charset=utf-8', null, 'Content-Type');
        $cs->registerMetaTag('index, follow', 'robots');
        self::seoTags();
        // self::addFavicon();
    }

    public static function seoTags($metadata = array())
    {
        $meta_key  = Yii::app()->controller->meta_key;
        $meta_desc = Yii::app()->controller->meta_desc;

        $cs = Yii::app()->clientScript;

        if (!empty($meta_key))
            $cs->registerMetaTag($meta_key, 'keywords');

        if (!empty($meta_desc))
            $cs->registerMetaTag($meta_desc, 'description');
    }

    private static function addFavicon()
    {
        $cs   = Yii::app()->clientScript;
        $file = Yii::app()->theme->basePath .DS. 'favicon.png';

        if (is_file($file)) {
            $cs->registerLinkTag('icon', 'image/png', Yii::app()->theme->baseUrl.'/favicon.png');
            $cs->registerLinkTag('icon', 'image/x-icon', Yii::app()->theme->baseUrl.'/favicon.ico');
        } else {
            $cs->registerLinkTag('icon', 'image/png', '/favicon.png');
        }
    }
}
