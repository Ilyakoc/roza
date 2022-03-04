<?php
/**
 * Действие загрузки файла для плагина jQuery Upload File
 * @use common.vendors.jQuery-File-Upload.server.php.UploadHandler
 */
namespace common\ext\file\actions;

use common\components\helpers\HArray as A;
use common\components\helpers\HFile;

\Yii::import('common.vendors.jQuery-File-Upload.server.php.UploadHandler');

class JQueryUploadFileAction extends \CAction
{
	/**
	 * @var string|NULL путь к папке, в которую будут загружены файлы.
	 * Будет перезаписано из $options['upload_dir'], если передано.
	 */
	public $uploadDir=null;
	
	/**
	 * @var string|NULL URL к загруженным файлам.
	 * Будет перезаписано из $options['upload_url'], если передано.
	 */
	public $uploadUrl=null;
	
	/**
	 * @var string|NULL имя REQUEST-параметра.
	 * Будет перезаписано из $options['param_name'], если передано.
	 * По умолчанию (NULL) будет получено первое из _FILES.
	 */
	public $paramName=null;
	
	/**
	 * @var array параметры настройки загрузчика \UploadHandler.
	 */
	public $options=[];
	
	/**
	 * Запуск действия
	 */
	public function run()
	{
		if(!A::get($this->options, 'upload_dir') && $this->uploadDir) {
			$this->options['upload_dir']=$this->uploadDir;
		}
		if(!A::get($this->options, 'upload_url') && $this->uploadUrl) {
			$this->options['upload_url']=$this->uploadUrl;
		}
				
		if(!A::get($this->options, 'param_name') && $this->paramName) {
			$this->options['param_name']=$this->paramName;
		}
		elseif(A::get($this->options, 'param_name')) {
			$this->paramName=$this->options['param_name'];
		}
		else {
			$this->paramName=key($_FILES);
			$this->options['param_name']=$this->paramName;
		}
		
		$hUpload = new \UploadHandler($this->options);
		
// 		$response=$hUpload->get_response();
// 		if(isset($response[$this->paramName])) {
// 			foreach($response[$this->paramName] as $file) {
				
// 			}
// 		}
// 		print_r($response);
		exit;
	}
}