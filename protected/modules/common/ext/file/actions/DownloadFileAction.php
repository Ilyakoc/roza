<?php
/**
 * Действие загрузки файла
 *
 * Добавить правило: '/download/<filename:.*>'=>'/controller/downloadFile'
 */
namespace common\ext\file\actions;

use common\components\helpers\HYii as Y;
use common\components\helpers\HFile;

class DownloadFileAction extends \CAction
{
	/**
	 * @var array разрешенные директории для скачивания файлов.
	 * По умолчанию (пустой массив) разрешенных директорий нет. 
	 */
	public $allowDirs=[];
	
	/**
	 * Запуск действия
	 * @param string $filename путь и имя файла для скачивания
	 */
	public function run($filename)
	{
		$filename=preg_replace('#/+#', Y::DS, urldecode($filename));
		
		if($this->isAllow($filename)) {
			$filename=\Yii::getPathOfAlias('webroot') . Y::DS . $filename;
			HFile::download($filename);
		}
		
		header("HTTP/1.0 404 Not Found");
		exit;
	}
	
	/**
	 * Файл разрешен для скачивания
	 * @param string $filename путь и имя файла для скачивания
	 */
	private function isAllow($filename)
	{
		$exprDirs=preg_replace('#[/|\\\\]+#', '/', implode('|', $this->allowDirs));
		$filename=preg_replace('#[/|\\\\]+#', '/', $filename);
		return preg_match('#^('.$exprDirs.')/#', $filename);
	}
}