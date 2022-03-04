<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/defaults.php'),
	array(
		'timeZone'=>'Asia/Novosibirsk',
		'components'=>array(
			'db'=>array(
    			'connectionString'=>'mysql:host=localhost;dbname=cl30234_broza',
                'username'=>'cl30234_broza',
                'password'=>'riCfhSp9',
                'initSQLs'=>array("SET time_zone = '+7:00'")
			),
		),
		'theme'=>isset($_GET['dbg'])?'template_11':'template_11',
		'params'=>array(
			'dev_year'=>2014,
			'shopPayment'=>array(
				'robokassa'=>array(
	            	'login'=>'bazaroza.ru',
	            	'password1'=>'ScXBbzhxA570',
	            	'password2'=>'O6hApInOkKsN',
	            	//'url'=>'http://test.robokassa.ru/Index.aspx',
	            	'url'=>'https://merchant.roboxchange.com/Index.aspx',
	        	),
				'actions'=>array(
					2=>array('url'=>'/shop/payment'),					
				),
				'types'=>array(
					1=>'Наличными при получении (г. Новосибирск)',
					2=>'On-line оплата'
				),
			),
			'deliveryTypes'=>array(
				2=>'Доставка курьером',
			),
			'slider'=> array(
				'showCaption' => false,
	                'slideshow' => array(
        	            'width'  => 9999999, 
                	    'height' => 455,
                	),
            	),
		),
	)
);
