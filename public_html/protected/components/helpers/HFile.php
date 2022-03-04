<?php
/**
 * File helper
 * 
 * @version 1.0
 */
use HYii as Y;
use HArray as A;

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
	public static function mkDir($pathname, $mode=0777, $recursive=false)
	{ 
		return is_dir($pathname) ? true : mkdir($pathname, $mode, $recursive);
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
	 * Get only files in directory
	 * @param string $path path to directory.
	 * @return array
	 */
	public static function getFiles($path)
	{
		$files = [];
		
		if(is_dir($path)) {
			$d = dir($path);
			while (false !== ($entry = $d->read())) {
				if(is_file($path . DIRECTORY_SEPARATOR . $entry))
					$files[] = $entry;
			}
			$d->close();
		}
	
		return $files;
	}

	/**
	 * Получить путь
	 * @param array $routes массив путей для склейки.
     * @param boolean $mkdir создавать директорию, если не существует. 
     *  По умолчанию (FALSE) - не создавать.
	 */
	public static function path($routes, $mkdir=false, $dirmode=0777)
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
    
    public static function thumb($src, $width, $height, $cacheTime=0, $forcy=false, $isFile=false)
    {
		if($isFile) $file=$src;
        else $file=$_SERVER['DOCUMENT_ROOT'] . $src;
        
        if(is_file($file)) {
        	$salt = hash_file('md5', $file);
            $tmb="{$salt}_{$width}_{$height}_".basename($file);
            
            // $tmb="{$width}_{$height}_".basename($file);
            $tmbFile=self::path([dirname($file), $tmb]);

            if(!$forcy && is_file($tmbFile)) {
                $forcy=($cacheTime > 0) ? ((time() - filectime($tmbFile)) > $cacheTime) : true;
            }
            
            if(!is_file($tmbFile) || $forcy) {
                \Yii::app()->ih->load($file)->resize($width, $height, true)->save($tmbFile);
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
     *  new - файл арзива будет создан заново, иначе файлы будут 
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
} 