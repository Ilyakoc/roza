<?php
/**
 * Виджет формы загрузки файлов jQuery плагина jQuery-File-Upload.
 * @link https://github.com/blueimp/jQuery-File-Upload
 * 
 * @use common.vendors.jQuery-File-Upload
 */
namespace common\ext\file\widgets;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HFile;

class JQueryFileUpload extends \CWidget
{
	/**
	 * @var string jQuery path-выражение для выборки DOM-элемента,
	 * для которого применяется плагин. 
	 */
	public $selector;
	
	/**
	 * @var string ссылка на действие загрузки файла.
	 * Действие может быть подключено в контроллер 
	 * \common\ext\file\actions\jQueryUploadFileAction
	 * Будет перезаписан, параметром $options["url"].
	 */
	public $url;
	
	/** 
	 * @var array опции для плагина jQuery-File-Upload
	 */ 
	public $options;
	
	/**
	 * @var boolean использовать режим одиночной загрузки.
	 * По умолчанию (FALSE) установлен режим многофайловой загрузки. 
	 * Будет перезаписан, параметром $options["singleFileUploads"].
	 */
	public $single=false;
	
	/**
	 * @var boolean использовать режим jQuery-UI.
	 * По умолчанию (FALSE) - не использовать.
	 * @todo данный функционал не работает.
	 */
	public $ui=false;
	
	/**
	 * @var string|NULL путь к пользовательскому файлу стилей формы загрузки.
	 * Если передан будет использован вместо стилей поставляемых в плагине.
	 * По умолчанию (NULL) - использовать стили поставляемые в плагине. 
	 */
	public $cssFile=null;
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		if($this->cssFile) {
			Y::css($this->cssFile);
		}
		else {
			Y::publish([
				'path'=>Y::getPathOfAlias('common.vendors.jQuery-File-Upload.css'),
				'css'=>$this->ui ? 'jquery.fileupload-ui.css' : 'jquery.fileupload.css'
			]);
		}
		
		$js=[
			'load-image.all.min.js', 
			'vendor/jquery.ui.widget.js', 
			'cors/jquery.postmessage-transport.js',
			'cors/jquery.xdr-transport.js',
			'jquery.iframe-transport.js',
			'jquery.fileupload.js',
			'jquery.fileupload-process.js',
			'jquery.fileupload-validate.js',
			'jquery.fileupload-audio.js',
			'jquery.fileupload-image.js',
			'jquery.fileupload-video.js'
		];
		if($this->ui) {
			$js[]='jquery.fileupload-ui.js';
			$js[]='jquery.fileupload-jquery-ui.js';
		}		
		Y::publish([
			'path'=>Y::getPathOfAlias('common.vendors.jQuery-File-Upload.js'),
			'js'=>$js
		]);
		
		if(!A::get($this->options, 'url')) {
			$this->options['url']=$this->url;
		}
		
		if(!$single && !A::get($this->options, 'singleFileUploads')) {
			$this->options['singleFileUploads']=false;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		Y::js(
			'jquf'.crc32($this->selector), 
			'$("'.$this->selector.'").fileupload('.\CJavaScript::encode($this->options).')', 
			\CClientScript::POS_READY
		);
	}
}