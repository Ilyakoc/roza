<?php

class newsBlock extends CWidget
{
	public function run()
	{
		$criteriaNews = new CDbCriteria();
		$criteriaNews->compare('type', 'news');
		$criteriaNews->order = 'id DESC';
		$countNews = Event::model()->count($criteriaNews);
		$oneNew = Event::model()->findByAttributes(array('type'=>'news'),array('order'=>'id DESC'));
		
		$criteriaArticle = new CDbCriteria();
		$criteriaArticle->compare('type', 'article');
		$criteriaArticle->order = 'id DESC';
		$countArticle = Event::model()->count($criteriaArticle);
		$oneArticle = Event::model()->findByAttributes(array('type'=>'article'),array('order'=>'id DESC'));
		
		$criteriaWiki = new CDbCriteria();
		$criteriaWiki->compare('type', 'wiki');
		$criteriaWiki->order = 'id DESC';
		$countWiki = Event::model()->count($criteriaWiki);
		$oneWiki = Event::model()->findByAttributes(array('type'=>'wiki'),array('order'=>'id DESC'));

		
		$this->render('default', compact('countNews', 'countArticle', 'countWiki', 'oneArticle', 'oneWiki', 'oneNew'));
	}
}
