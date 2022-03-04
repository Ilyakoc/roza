<?php
/**
 * File helper
 * 
 * @version 1.0
 */
namespace common\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class HFile
{
	/**
	 * Отправка HTTP-заголовков на загрузку файла
	 * @param string $filename имя файла.
	 * @param string|NULL $content содержимое. По умолчанию (NULL) контент
	 * берется из файла (если $filename указывает на существующий файл).
	 * @param boolean $allowEmpty разрешить скачивание пустых файлов. По умолчанию (FALSE) запрещено.
	 * @param boolean $deniedFile запретить скачивание файлов. Может быть востребовано, когда 
	 * разрешено скачивание только передаваемого в метод контента ($content). 
	 * По умолчанию (FALSE) скачивание файлов разрешено.
	 */
	public static function download($filename, $content=null, $allowEmpty=false, $deniedFile=false)
	{
		$filename=preg_replace('#/+#', Y::DS, urldecode($filename));
		
		$contentDispositionFilename=null;
		if($content !== null) {
			$contentDispositionFilename=pathinfo($filename, PATHINFO_BASENAME);
			$contentLength=strlen($content);
		}
		elseif(($content === null) && is_file($filename) && !$deniedFile) {
			$contentDispositionFilename=pathinfo($filename, PATHINFO_BASENAME);
			$contentLength=filesize($filename);
			$content=file_get_contents($filename);
		}
		
		if(!empty($contentDispositionFilename) && ($contentLength || ($allowEmpty && !$contentLength))) {
			header("HTTP/1.1 200 OK");
			header("Connection: close");
			header("Content-Type: application/octet-stream");
			header("Accept-Ranges: bytes");
			header("Content-Disposition: Attachment; filename=".$contentDispositionFilename);
			header("Content-Length: ".$contentLength);
		
			echo $content;
			exit;
		}
		else {
			header("HTTP/1.0 404 Not Found");
		}		
		exit;
	}
	
	/**
	 * Get file extension
	 * @param string $filename file name.
	 * @return string
	 */
	public static function getExt($filename)
	{
		return pathinfo($filename, PATHINFO_EXTENSION);
	}
	
	/**
	 * Получает только имя файла
	 * @param string $filename имя файла
	 */
	public static function getFileName($filename)
	{
		return pathinfo($filename, PATHINFO_FILENAME);
	}
	
	/**
	 * Удаляет расширение файла
	 * @param string $filename имя файла
	 * @param boolean $extDotCount кол-во точек в расширении. По умолчанию 0 (нуль).
	 */
	public static function removeExt($filename, $extDotCount=0)
	{
		return preg_replace('/^(.*?)(\.[^\.]+){0,' . ((int)$extDotCount + 1) . '}$/', '\\1', $filename);
	}
	
	/**
	 * File exists
	 * @param string $filename file name.
	 * @param boolean $notEmpty может ли файл быть пустым? По умолчанию FALSE - может.  
	 * @return boolean
	 */
	public static function fileExists($filename, $notEmpty=false) 
	{
		return is_file($filename) && (!$notEmpty || (filesize($filename) > 0));
	}
	
	/**
	 * Проверка является ли файл изображением.
	 * @param string $filename имя файла.
	 * @return boolean
	 */
	public static function fileExistsByImage($filename)
	{
		return self::fileExists($filename, true) && exif_imagetype($filename);
	}
	
	/**
	 * Get directory path
	 * @param string $path Path with filename
	 * @param boolean $close завершить путь DIRECTORY_SEPARATOR или нет.
	 * @return string
	 */
	public static function getDir($path, $close=false)
	{
		return pathinfo($path, PATHINFO_DIRNAME) . ($close ? HYii::DS : '');
	}
	
	/**
	 * Make dir
	 * @see mkdir()
	 * Отличие в том, что происходит проверка того, создана ли директория или нет,
	 * и не принимает 4-го параметра $context.
	 */
	public static function mkDir($pathname, $mode=0755, $recursive=false)
	{ 
		return is_dir($pathname) ? true : mkdir($pathname, $mode, $recursive);
	}
	
	/**
	 * Удаление файла или директории
	 * @param string $entry имя файла или директории (полный путь)
	 * @param boolean $recursive рекурсивное удаление. По умолчанию false.
	 * @param boolean $keepself не удалять переданную директорию.
	 * Используется, если требуется удалить только все содержимое директории. 
	 * По умолчанию false.
	 * @return array список удаленных директорий и файлов.
	 */
	public static function rm($entry, $recursive=false, $keepself=false)
	{
		$removed=[];
		
		if(is_link($entry) || is_file($entry)) {
			if(!$keepself) {
				unlink($entry);
				$removed[]=$entry;
			}
		}
		else {
			$fRemoveEntry=function($dirname, $entry, $params) use (&$fRemoveEntry, &$removed) {
				if(is_link($entry) || is_file($entry)) {
					unlink($entry);
					$removed[]=$entry;
				}
				elseif(is_dir($entry)) {
					self::readDir($entry, true, $fRemoveEntry, $params);
					rmdir($entry);
					$removed[]=$entry;
				}
			};
			self::readDir($entry, true, $fRemoveEntry, compact('recursive'));
			
			if(!$keepself && is_dir($entry)) {
				rmdir($entry);
				$removed[]=$entry;
			}
		}
		
		return $removed;
	}
	
	/**
	 * Преобразовать путь в URL
	 * на данный момент просто заменят символы "/" или "\" в "/".
	 * @param string $path путь
	 * @return string 
	 */
	public static function pathToUrl($path)
	{
		return preg_replace('/[\/\\\\]+/', '/', $path); 
	}
	
	/**
	 * Прочитать директорию.
	 * В результат не попадают записи "." и "..".
	 * @param string $dirname полный путь к директории
	 * @param boolean $fullpath возвращать полный записи с полным путем.
	 * Если будет передано false, то будут возвращены только имена записей.
	 * (если не задан обработчик записей)
	 * @param callable|false $hProcessing обработчик результата.
	 * Параметры обработчика array(dirname, entry, params), где
	 * dirname - текущая директория
	 * entry - значение записи. Если задано $fullpath=true будет 
	 * передано значение записи с полным путем.
	 * params - дополнительные параметры
	 * Обработчик должен возвращать результат обработки записи.
	 * Если будет возвращено значение null, результат обработчика 
	 * не будет добавлен в итоговый результат обработки записей. 
	 * По умолчанию (false) не задан. 
	 * @param array $params дополнительные параметры для обработчика.
	 * @return array результат обработки записей 
	 */
	public static function readDir($dirname, $fullpath=true, $hProcessing=false, $params=[])
	{
		$entries = [];
		
		if(is_dir($dirname)) {
			$d = dir($dirname);
			while (false !== ($entry = $d->read())) {
				if(($entry != '.') && ($entry != '..')) {
					if($fullpath) {
						$entry=$dirname. DIRECTORY_SEPARATOR . $entry;
					}
					if(is_callable($hProcessing)) {
						$result=call_user_func_array($hProcessing, [$dirname, $entry, $params]);
						if($result !== null) {
							$entries[]=$result;
						}
					}
					else {
						$entries[]=$entry;
					}
				}
			}
			$d->close();
		}
		
		return $entries;
	}
	
	/**
	 * Get only files in directory
	 * @param string $dirname path to directory.
	 * @param boolean $fullpath возвращать полный путь. 
	 * По умолчанию (false), только имена файлов.
	 * @return array
	 */
	public static function getFiles($dirname, $fullpath=false)
	{
		$files = [];
		
		if(is_dir($dirname)) {
			$d = dir($dirname);
			while (false !== ($entry = $d->read())) {
				if(is_file($dirname. DIRECTORY_SEPARATOR . $entry)) {
					if($fullpath) {
						$files[] = $dirname. DIRECTORY_SEPARATOR . $entry;
					}
					else {
						$files[] = $entry;
					}
				}
			}
			$d->close();
		}
	
		return $files;
	}
	
	/**
	 * Get only directories by directory
	 * @param string $dirname path to directory.
	 * @param boolean $fullpath возвращать полный путь.
	 * По умолчанию (false), только имена директорий.
	 * @return array
	 */
	public static function getDirs($dirname, $fullpath=false)
	{
		return self::readDir($dirname, false, function($dirname, $entry, $params) {
			$filename=$dirname . DIRECTORY_SEPARATOR . $entry;
			if(!is_link($filename) && is_dir($filename)) {
				if(A::get($params, 'fullpath', false)) {
					return $filename;
				}
				else {
					return $entry;
				}
			}
		}, compact('fullpath'));
	}

	/**
	 * Получить путь
	 * @param array $routes массив путей для склейки.
     * @param boolean $mkdir создавать директорию, если не существует. 
     *  По умолчанию (FALSE) - не создавать.
	 */
	public static function path($routes, $mkdir=false, $dirmode=0755)
	{
		$path=implode(Y::DS, $routes);
        
        if($mkdir) {
            if(!is_dir(dirname($path))) {
                self::mkDir(dirname($path), $dirmode, true);
            }
        }
        
        return $path;
	}
    
	public static function getBaseUrl($src)
    {
        return preg_replace('/^(.*)[\\\\\/]([^\\\\\/]+)$/', '$1', $src);
    }
    
    public static function thumb($src, $width, $height, $cacheTime=0, $forcy=false, $isFile=false, $adaptive=false)
    {
		if($isFile) $file=$src;
        else $file=$_SERVER['DOCUMENT_ROOT'] . $src;
        
        if(is_file($file)) {
            $tmb="{$width}_{$height}_".basename($file);
            $tmbFile=self::path([dirname($file), $tmb]);
            if(!$forcy && is_file($tmbFile)) {
                $forcy=($cacheTime > 0) ? ((time() - filectime($tmbFile)) > $cacheTime) : true;
            }
            if(!is_file($tmbFile) || $forcy || YII_DEBUG) {
            	$image=\Yii::app()->ih->load($file);
            	if($adaptive) {
            		$image=$image->adaptiveThumb($width, $height);
            	}
            	else {
            		$image=$image->resize($width, $height, true);
            	}
                $image->save($tmbFile);
            }
            
            return self::getBaseUrl($src) . '/'. $tmb;
        }
        
        return null;
    }
    
    /**
     * Архивирование файла
     * @param array $options 
     *  file - имя файла, который необходимо заархивировать;
     *  content - содержимое, которое необходимо заархивировать;
     *  local - имя файла в архиве. По умолчанию NULL; 
     *  zipfile - имя файла архива. По умолчанию NULL;
     *  forcy - обязательно создавать файл, иначе если архивный файл 
     *  сущесвует будет возвращен он. По умолчанию FALSE;
     *  zipdir - путь до директории, в которой создается zip архив. 
     *  Требуется только если не переданы zipfile и file. 
     *  По умолчанию "{DOCUMENT_ROOT}/files/zip/"
     *  new - файл архива будет создан заново, иначе файлы будут 
     *  добавляться в архив. По умолчанию (TRUE).
     *
     * Является обязательным одна из двух опций "file" или "content".
     */
   	public static function zip($options)
	{
        $content=null;
        
        if(!($file=A::get($options, 'file'))) {
            if(!($content=A::get($options, 'content'))) {
                return false;
            }
        }
        
        $zipFile=A::get($options, 'zipfile');
        
        if(!($localName=A::get($options, 'local'))) {
            if(is_file($file)) {
                $info=pathinfo($file);
                $localName=$info['filename'] . '.' . $info['extension'];
                if(!$zipFile) {
                    $zipFile=$info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . date('_d_m_Y') . '.zip';
                }
            }
            else {
                $localName='file' . date('_d_m_Y') . '.txt';
                if(!$zipFile) {
                    $zipFile=self::path(
                        A::get($options, 'zipdir', HFile::path($_SERVER['DOCUMENT_ROOT'], 'files', 'zip')),
                        'file' . date('_d_m_Y') . '.zip'
                    );
                }
            }
        }
        
        if(!$zipFile) {
            $zipFile=self::path(
                A::get($options, 'zipdir', HFile::path($_SERVER['DOCUMENT_ROOT'], 'files', 'zip')),
                $localName . '.zip'
            );
        }
        
        if(is_file($zipFile) && !A::get($options, 'forcy', false)) {
			return $zipFile;
		}

		$zip=new \ZipArchive();
        $zipFlags=A::get($options, 'new', true) ? \ZipArchive::OVERWRITE : \ZipArchive::CREATE;
		if($zip->open($zipFile, $zipFlags)) {
            if($content) $zip->addFromString($localName, $content);
            else $zip->addFile($file, $localName);
			$zip->close();
		}
        
		if(file_exists($zipFile)) {
			return $zipFile;
		}

		return false;
	}
	
	/**
	 * Добавление файла в zip-архив.
	 * @param \ZipArchive &$zip объект класса работы с zip-архивом.
	 * @param string $filename путь к файлу для добавления. 
	 * @param string|null $localname имя файла внутри ZIP-архива. Если указано, то переопределит $filename.
	 * @param boolean $convert конвертировать имя файла из utf-8 в cp866. По умолчанию (true) конвертировать.
	 * @return boolean Возвращает TRUE в случае успешного завершения или FALSE в случае возникновения ошибки. 
	 */
	public static function zipAddFile(&$zip, $filename, $localname=null, $convert=true)
	{
		if($convert) {
			//$filename=iconv('utf-8', 'cp866', $filename);
			if($localname) {
				$localname=iconv('utf-8', 'cp866', $localname);
			}
		}
		
		return $zip->addFile($filename, $localname);
	}
	
	/**
	 * Открытие zip-архива
	 * @param string|array $filename имя файла архива.
	 * @param integer|boolean $flags Используемый режим открытия файлов.
	 * По умолчанию ZipArchive::OVERWRITE 
	 * @return \ZipArchive объект класса работы с zip-архивом.
	 */
	public static function zipOpen(&$filename, $flags=false)
	{
		if(is_array($filename)) {
			$filename=self::path($filename, true, 0755);
		}
		
		if($flags === false) {
			$flags=\ZipArchive::OVERWRITE;
		}
		
		$zip = new \ZipArchive;
		
		if($zip->open($filename, $flags)) {
			return $zip;
		}
		
		return false;
	}
	
    /**
     * Создание CSV файла
     * @param string $file
     * @param array $data
     */
    public static function csv($file, $data, $delimiter=';', $enclosure='"', $escape_char='\\', $withBOM=false)
    {
        if($fp=fopen($file, 'w+')) {
            if($withBOM) {
            //add BOM to fix UTF-8 in Excel
                fputs($fp, ( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
            }
            if(is_array($data)) {
                foreach($data as $row) {
                    fputcsv($fp, $row, $delimiter, $enclosure); //, $escape_char);
                }
            }
            
            fclose($fp);
            
            return true;
        }
        
        return false;
    }

	/**
	 * Include file
	 * Если файл не найден, то возращается значение по умолчанию.
	 * @param string $filename file name.
	 * @param mixed $default значение, которое возвращать, если файл не найден. По умолчанию NULL.
	 * @return mixed Если файл не найден, возвращется значение переданное в параметре $default.
	 */
	public static function includeFile($filename, $default=null)
	{
		return is_file($filename) ? include($filename) : $default;
	}
	
	/**
	 * Include file by path alias
	 * Если файл не найден, то возращается значение по умолчанию.
	 * @param string $alias path alias.
	 * @param mixed $default значение, которое возвращать, если файл не найден. По умолчанию NULL.
	 * @param array|NULL $extract переменные, которые необходимо импортировать для использования в 
	 * подключаемом файле. По умолчанию (NULL) - нет импортируемых переменных.
	 * @param string $ext расширение файла. По умолчанию ".php".
	 * @return mixed Если файл не найден, возвращется значение переданное в параметре $default.
	 */
	public static function includeByAlias($alias, $default=null, $extract=null, $ext='.php')
	{
		$filename=\Yii::getPathOfAlias($alias) . $ext;
		if(is_file($filename)) {
			if(is_array($extract)) extract($extract);
			return include($filename);
		}
		return $default;
	}
	
	/**
	 * Get the directory size
	 * @link https://helloacm.com/get-files-folder-size-in-php/
	 * @param directory $directory
	 * @param boolean $returnFormat возвратить результат отформатированным
	 * @return integer
	 */
	public static function getDirSize($directory, $returnFormat=false) {
		$size = 0;
		if(is_dir($directory)) {
			foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
				$size += $file->getSize();
			}
		}
		
		if($returnFormat) {
			return self::formatSize($size);
		}
		
		return $size;
	}
	
	/**
	 * Форматированный вывод размера файла/директории
	 * @link http://stackoverflow.com/a/16765613
	 * @param integer $B размер
	 * @param number $D размер дробной части. По умолчанию 2(два числа) 
	 * @return string
	 */
	public static function formatSize($B, $D=2){
		$S = 'kMGTPEZY';
		$F = floor((strlen($B) - 1) / 3);
		return sprintf("%.{$D}f", $B/pow(1024, $F)).' '.@$S[$F-1].'B';
	}
	
	/**
	 * Получить размер в байтах
	 * @param string $size строка размера (G,M,K)
	 * @return number
	 */
	public static function getSizeBytes($size)
	{
		$size=trim($size);
		$last=strtolower($size[strlen($size)-1]);
		switch($last) {
			case 'g':
				$size*= 1024;
			case 'm':
				$size*= 1024;
			case 'k':
				$size*= 1024;
		}
			
		return $size;
	}
    
    /**
     * Публикация ресурсов для отложенной загрузки изображений
     * Размещать в шаблоне, например в <head>
     * \common\components\helpers\HFile::publishLazyLoad();
     */
    public static function publishLazyLoad()
    {
        Y::publish([
            'path'=>Y::module('common')->getAssetsBasePath(),
            'js'=>'js/vendors/jquery_lazyload/lazyload.min.js'
        ]);
        Y::js('lazyload', '$("img[data-lazyload]").lazyload();', \CClientScript::POS_READY);
    }
    
    /**
     * Обработка содержимого для отложенной загрузки изображений
     * Обернуть программный код приложения
     * ob_start()
     * ... программный код приложения ...
     * if(\Yii::app()->user->isGuest) {
	 *   echo \common\components\helpers\HFile::prepareLazyLoad(ob_get_clean());
     * }
     * else {
	 *   ob_end_flush();
     * }
     */
    public static function prepareLazyLoad($content, $preview=false)
    {
        if(!$preview) {
            $preview=Y::module('common')->getAssetsBaseUrl().'/js/vendors/jquery_lazyload/preview.png';
        }
        
        return preg_replace('#<img([^>]*?) src="([^\s>]+)"([^>]*?)>#i', '<img\1 src="'.$preview.'" data-lazyload="1" data-src="\2"\3>', $content);
    }
} 
