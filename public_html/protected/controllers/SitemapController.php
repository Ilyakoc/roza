<?php

class SitemapController extends Controller
{
    public function actionIndex()
    {
		$urls = array();
		
		$tzd = date('Z');
		
		// Страницы
        $pages = Page::model()->findAll();
        foreach ($pages as $page){
			$dateLastmod = date('Y-m-d\TH:i:s',strtotime($page->update_time));
			$dateLastmod .= ($tzd < 0)? "-".gmdate('H:i', -$tzd) : "+".gmdate('H:i', $tzd);
			if(($page->alias) == 'glavnaia'){
				$urls[] = array('loc' => '', 'priority' => '1.0','lastmod'=>$dateLastmod);
			}else{
				$urls[] = array('loc' =>$page->alias, 'priority' => '0.9','lastmod'=>$dateLastmod);
			}
        }
		
		//Категории
		$categories = Category::model()->findAll(array('order'=>'ordering'));
        foreach ($categories as $category){
			$dateLastmod = date('Y-m-d\TH:i:s',strtotime($category->update_time));
			$dateLastmod .= ($tzd < 0)? "-".gmdate('H:i', -$tzd) : "+".gmdate('H:i', $tzd);
			$urls[] = array('loc' => $category->alias, 'priority' => '0.9','lastmod'=>$dateLastmod);
        }
		//Товары
		$products = Product::model()->findAll(array('order'=>'ordering'));
        foreach ($products as $product){
			$dateLastmod = date('Y-m-d\TH:i:s',strtotime($product->update_time));
			$dateLastmod .= ($tzd < 0)? "-".gmdate('H:i', -$tzd) : "+".gmdate('H:i', $tzd);
			$urls[] = array('loc' => $product->alias, 'priority' => '0.8','lastmod'=>$dateLastmod);
        }
		
		$dateLastmod = date('Y-m-d\TH:i:s');
		$dateLastmod .= ($tzd < 0)? "-".gmdate('H:i', -$tzd) : "+".gmdate('H:i', $tzd);
		// Новости
		$criteriaEvents = new CDbCriteria();
        $criteriaEvents->condition = 'publish = 1';
        $criteriaEvents->compare('type', 'news');
        $criteriaEvents->order     = 'created DESC';
		$events = Event::model()->findAll($criteriaEvents);
        foreach ($events as $event){
			$urls[] = array('loc' =>($event->type).'/'.($event->id), 'priority' => '0.8','lastmod'=>$dateLastmod);
        }
		if(count($events) > 0){
			$urls[] = array('loc' =>'news', 'priority' => '0.9','lastmod'=>$dateLastmod);
		}
		
		$host = Yii::app()->request->hostInfo;
		
		$dom = new domDocument("1.0", "utf-8"); // Создаём XML-документ версии 1.0 с кодировкой utf-8
		$rootxml = $dom->createElement("urlset"); // Создаём корневой элемент
		$rootxml->setAttribute("xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9");
		$rootxml->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
		$rootxml->setAttribute("xsi:schemaLocation", "http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd");
		$dom->appendChild($rootxml);
		foreach ($urls as $url){
			$urlxml = $dom->createElement("url");
			$locxml = $dom->createElement("loc", $host .'/'. $url["loc"]);
			$lastmodxml = $dom->createElement("lastmod", $url["lastmod"]);
			$changefreqxml = $dom->createElement("changefreq", "daily");
			$priorityxml = $dom->createElement("priority", $url["priority"]);
			$urlxml->appendChild($locxml);
			$urlxml->appendChild($lastmodxml);
			$urlxml->appendChild($changefreqxml);
			$urlxml->appendChild($priorityxml);
			$rootxml->appendChild($urlxml);
		}
		$dom->formatOutput = true;

		$dom->save($_SERVER['DOCUMENT_ROOT'].'/sitemap.xml'); // Сохраняем полученный XML-документ в файл

    }
}
