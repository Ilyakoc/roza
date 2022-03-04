<?php

class ResizeHelper
{
    /**
     * Resize image
     * @static
     * @param $fullPath
     * @param $width
     * @param $height
     * @return mixed
     */
    public static function resize($fullPath, $width, $height, $top = false)
    {
    	if (!$fullPath) {
    		return $fullPath;
    	}

        $name = explode('?', $fullPath);
        $name = $name[0];

        $parts = explode('/', $name);
        $filename = array_pop($parts);
        $path = implode('/', $parts);

        $fullPath = Yii::getPathOfAlias('webroot') . $path;

        if(!is_file($fullPath . DS . $filename)) return null;

        $salt = hash_file('md5', $fullPath . DS . $filename);

        $resizeFilename = $salt . '_' . $width . '_' . $height . '_' . $filename;

        $ih = new CImageHandler();

        if(!file_exists($fullPath . DS . $resizeFilename)) {
            $ih
                ->load($fullPath . DS . $filename);

            if(!$width || !$height) {
                $ih->resize($width, $height);
            } else {
                $ih->adaptiveThumb($width, $height, $top);
            }

            $ih->save($fullPath . DS . $resizeFilename, false, 95);
        }

        return $path . DS . $resizeFilename;

    }

    /**
     * Resize image
     * @static
     * @param $fullPath
     * @param $width
     * @param $height
     * @return mixed
     */
    public static function watermark($fullPath, $zoom = false, $force = false)
    {
        if (!$fullPath) {
            return $fullPath;
        }

        if (strpos($fullPath, "?")) {
            $fullPath = substr($fullPath, 0, strpos($fullPath, "?"));
        }

        $parts = explode(DIRECTORY_SEPARATOR, $fullPath);
        $filename = array_pop($parts);
        $path = implode(DIRECTORY_SEPARATOR, $parts);

        $fullPath = Yii::getPathOfAlias('webroot') . $path;

        if(!is_file($fullPath . DS . $filename)) return null;

        $salt = hash_file('md5', $fullPath . DS . $filename);

        $fileParts = explode('.', $filename);

        $resizeFilename = 'water_' . $salt . '.' . end($fileParts);

        $ih = new CImageHandler();

        if(!file_exists($fullPath . DS . $resizeFilename) || $force) {
            $ih
                ->load($fullPath . DS . $filename);

            $ih->watermark($_SERVER['DOCUMENT_ROOT'] . '/images/watermark.png', 0, 0, CImageHandler::CORNER_CENTER, $zoom);

            $ih->save($fullPath . DS . $resizeFilename, false, 95);
        }

        return $path . DS . $resizeFilename;

    }
}
