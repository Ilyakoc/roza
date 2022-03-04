<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	public $layout='//layouts/other';

    public $menu = array();

    public $meta_key  = '';
    public $meta_desc = '';

	/**
	 * @var string текст заголовока контента страницы для H1.
	 */
	public $contentTitle='';
	
    /**
     * Main init method for cms
     * @return void
     */
    public function init()
    {
        CmsCore::checkDb();

        // Set site name
        $sitename   = Yii::app()->settings->get('cms_settings', 'sitename');
        $adminEmail = Yii::app()->settings->get('cms_settings', 'email');
        $menu_limit = Yii::app()->settings->get('cms_settings', 'menu_limit');
        $hide_news  = Yii::app()->settings->get('cms_settings', 'hide_news');

        if ($sitename)
            Yii::app()->name = $sitename;
        
        if ($adminEmail)
            Yii::app()->params['adminEmail'] = $adminEmail;

        if ($menu_limit)
            Yii::app()->params['menu_limit'] = $menu_limit;

        if ($hide_news)
            Yii::app()->params['hide_news'] = $hide_news;

        $this->meta_key  = Yii::app()->settings->get('cms_settings', 'meta_key');
        $this->meta_desc = Yii::app()->settings->get('cms_settings', 'meta_desc');

        $this->menu = CmsMenu::getInstance()->siteMenu();
    }

    /**
     * Set title of page
     * @param null|mixed $page_title
     * @return void
     */
    protected function prepareSeo($page_title = null)
    {
        $meta_title = Yii::app()->settings->get('cms_settings', 'meta_title');
        if (empty($meta_title))
            $meta_title = Yii::app()->name;

        if ($page_title === null)
            $this->pageTitle = $meta_title;
        else
            $this->pageTitle = $page_title;// .($meta_title ? (' - '. $meta_title) : '');
    }

    public function seoTags($metadata)
    {
    	if(($metadata instanceof CActiveRecord) && $metadata->hasRelated('meta')) {
    		$metadata=$metadata->getRelated('meta');
    	}
    	if($metadata instanceof Metadata) {
    		$this->meta_key=$metadata->getKey()?:$this->meta_key;
    		$this->meta_desc=$metadata->getDesc()?:$this->meta_desc;
    		$this->pageTitle=$metadata->getTitle()?:$this->pageTitle;
    		$this->contentTitle=$metadata->getH1()?:$this->contentTitle;
    	}
    	elseif ($metadata instanceof CModel
            && property_exists($metadata, 'key')
            && property_exists($metadata, 'desc')) {

            $metadata = array(
                'meta_key'=>$metadata['meta_key'],
                'desc'=>$metadata['desc'],
                'title'=>$metadata['title']?:$this->pageTitle
            );
        }

        if ($metadata['meta_key']) {
            $this->meta_key = $metadata['meta_key'];
        }
        if ($metadata->meta_desc) {
            $this->meta_desc = $metadata->meta_desc;
        }
        if ($metadata['title']) {
            $this->prepareSeo($metadata['title']);
        }
    }

    public function getTemplate()
    {
        return Yii::app()->theme->baseUrl;
    }

    public function isIndex()
    {
        return $this->id == 'site' && $this->action->id == 'index';
    }
}
