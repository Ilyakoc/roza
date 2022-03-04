<?php

class SearchController extends Controller
{
    public function actionAutoComplete() 
    {
        if (isset($_GET['q'])) {
            $query = Yii::app()->request->getQuery('q');
            $phrases=$this->getPhrases($query);

            $criteria = new CDbCriteria();
            $this->addSearchInCondition($criteria, 'title', $phrases);
            $this->addSearchInCondition($criteria, 'code', $phrases);
            $this->addSearchInCondition($criteria, 'description', $phrases);
            $criteria->limit = 10;
             
            $products = Product::model()->findAll($criteria);
             
            $resStr = '';
            foreach ($products as $product) {
                $resStr .= $product->title."\n";
            }
            echo $resStr;
        }
    }
    
	public function actionIndex()
	{
		$query = Yii::app()->request->getQuery('q');
		
		if (mb_strlen($query, 'UTF-8') < 3) {
			$this->prepareSeo('Слишком короткий запрос');
			$this->render('index_empty');
			return;
		}

    	$phrases=$this->getPhrases($query);
		
		// поиск по акциям (новостям)
		/*$criteria = new CDbCriteria();
		$this->addSearchInCondition($criteria, 'title', $phrases);
		$this->addSearchInCondition($criteria, 'text', $phrases);
		$criteria->addSearchCondition('publish', '1', false);
		
		$pagination = new CPagination();
		$pagination->pageSize = 3;
		$eventsDataProvider = new CActiveDataProvider('Event', array(
			'criteria'=>$criteria,
			'pagination' => $pagination
		));
		
		// поиск по страницам
		$criteria = new CDbCriteria();
		$this->addSearchInCondition($criteria, 'title', $phrases);
		$this->addSearchInCondition($criteria, 'intro', $phrases);
		$this->addSearchInCondition($criteria, 'text', $phrases);
		
		$pagination = new CPagination();
		$pagination->pageSize = 3;
		$pagesDataProvider = new CActiveDataProvider('Page', array(
			'criteria'=>$criteria,
			'pagination' => $pagination 
		));
		*/
		// поиск по продукции
		$criteria = new CDbCriteria();
		$this->addSearchInCondition($criteria, 'title', $phrases);
		$this->addSearchInCondition($criteria, 'description', $phrases);
		$this->addSearchInCondition($criteria, 'code', $phrases);


        $data_p = new CActiveDataProvider('Product', array(
            'sort'=>array(
                'defaultOrder'=>'t.title ASC , id DESC',
            ),
            'pagination'=>array(
                'pageSize' => 999,
                'pageVar' => 'p'
            ),
            'criteria'=>$criteria,
        ));
		$this->prepareSeo('Результаты поиска');
		//$this->breadcrumbs->add('Результаты поиска');

		if (Yii::app()->request->isAjaxRequest) {
			$this->renderPartial('index', compact(/*'eventsDataProvider', 'pagesDataProvider', */'data_p'));
		} else {
			$this->render('index', compact(/*'eventsDataProvider', 'pagesDataProvider', */'data_p'));
		}
	}
    
    protected function getPhrases($q) 
    {
        $q=preg_replace('/ +/', ' ', $q);
        return array_filter(explode(' ', $q), function($v) { return (strlen($v) > 2); });
    }
    
    protected function addSearchInCondition(&$criteria, $attribute, $phrases, $operator='OR') {
        $c=new CDbCriteria();
        if(!empty($phrases)) {
            foreach($phrases as $p) {
                $c->addSearchCondition($attribute, $p, true, 'AND');
            }
        }
        $criteria->mergeWith($c, $operator);
    }
}
