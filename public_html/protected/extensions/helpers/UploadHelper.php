<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 20.05.11
 * Time: 15:49
 * To change this template use File | Settings | File Templates.
 */

/**
 * @property object $model
 */
class UploadHelper
{
    const MODE_CROP = 1;

    public $uploadedModels = array();

    private $items    = array();
    private $uploaded = array();
    private $models   = array();

    static public $instance = null;
    static private $dirs    = array();

    public function __construct()
    {
        self::$dirs = array(
            'image'=>'images',
            'file'=>'files'
        );

        $this->models = array(
            'image'=>'CImage',
            'file'=>'File'
        );
    }

    /**
     * @static
     * @return UploadHelper
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;            
        }
        return self::$instance;
    }

    /*public static function getPath(& $model)
    {
        $root = YiiBase::getPathOfAlias('webroot');
        $type =

        $dir  = isset(self::$dirs[$type]) ? self::$dirs[$item->type] : 'files';

        return $root .DS.
    }*/

    /**
     * Upload files from array
     * @param array $params
     * @return null
     */
    public function runUpload($params = array())
    {
        if (!count($this->items))
            return;

        if (count($params))
            $this->upload()->resizeOnParams($params);
        else
            $this->upload()->resize();
    }

    public function add($toUpload, & $model, $type = 'image')
    {
        if (!is_array($toUpload))
            $toUpload = array($toUpload);

        if (!count($toUpload))
            return;

        if ($model instanceof CModel) {
            $model_name = get_class($model);
            $model_id   = $model->id;
        } else {
            $model_name = $model['model'];
            $model_id   = $model['id'];
        }

        foreach($toUpload as $item) {
            if ($item instanceof CUploadedFile == false) {
                throw new CException('Файл не соответствует');
            }

            $object = new stdClass();
            $object->file    = $item;
            $object->model   = strtolower($model_name);
            $object->item_id = $model_id;
            $object->type    = strtolower($type);

            $this->items[] = $object;
        }
    }

    public function createThumbnails($items, $params = array())
    {
        foreach ($items as $item) {
            $object = new stdClass();

            $object->type  = 'image';
            $object->path  = $item->path;
            $object->fname = $item->filename;
            $object->src   = $object->path .DS. $object->fname;

            $this->uploaded[] = $object;
        }

        if ($params)
            $this->resizeOnParams($params);
        else
            $this->resize();
    }

    public function createWatermark($item)
    {
        $watermark_file = Yii::getPathOfAlias('webroot.images').DS.'watermark.png';
        if (!is_file($watermark_file) || !is_file($item->src))
            return;

        Yii::app()->setComponents(array(
            'imagemod'=>array('class'=>'application.extensions.imagemodifier.CImageModifier')
        ));

        copy($item->src, $item->path .DS. 'src_'.$item->fname);

        $image = Yii::app()->imagemod->load($item->src);
        $image->image_watermark = $watermark_file;
        //$image->image_watermark_x = 10;
        //$image->image_watermark_y = -10;
        $image->jpeg_quality = 100;
        $image->file_new_name_body = $image->file_src_name_body;
        $image->file_overwrite = true;

        $image->process($item->path);
        return ($image->processed);
    }

    private function upload()
    {
        $root = YiiBase::getPathOfAlias('webroot');

        foreach ($this->items as $item) {
            $file = $item->file;
            $dir  = isset(self::$dirs[$item->type]) ? self::$dirs[$item->type] : 'files';

            $path = $root .DS. $dir .DS. $item->model;

            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            if ($item->type == 'image') {
                $filename = coreHelper::generateHash() .'.'. strtolower($file->extensionName);
            } else {
                $filename = strtolower($file->name);
            }

            $saved = $file->saveAs($path. DS .$filename);

            $model_name = $this->models[$item->type];

            $model = new $model_name;
            $model->model    = $item->model;
            $model->item_id  = $item->item_id;
            $model->filename = $filename;

            if ($saved && $model->save()) {
                // replace image rotate
                if ( class_exists("Imagick") && $item->type == 'image' ) {
                    $imageSRC = $path. DS .$filename;

                    $img = new Imagick(realpath($imageSRC));

                    $this->autorotate($img);
                    $img->stripImage(); // if you want to get rid of all EXIF data
                    $img->writeImage();
                }
                
                $uploaded = new stdClass();
                $uploaded->type  = $item->type;
                $uploaded->src   = $path. DS .$filename;
                $uploaded->fname = $filename;
                $uploaded->path  = $path;

                $this->uploaded[] = $uploaded;
                $this->uploadedModels[] = $model;
            }
        }

        return $this;
    }

    private function autorotate(Imagick $image)
    {
        switch ($image->getImageOrientation()) {
        case Imagick::ORIENTATION_TOPLEFT:
            break;
        case Imagick::ORIENTATION_TOPRIGHT:
            $image->flopImage();
            break;
        case Imagick::ORIENTATION_BOTTOMRIGHT:
            $image->rotateImage("#000", 180);
            break;
        case Imagick::ORIENTATION_BOTTOMLEFT:
            $image->flopImage();
            $image->rotateImage("#000", 180);
            break;
        case Imagick::ORIENTATION_LEFTTOP:
            $image->flopImage();
            $image->rotateImage("#000", -90);
            break;
        case Imagick::ORIENTATION_RIGHTTOP:
            $image->rotateImage("#000", 90);
            break;
        case Imagick::ORIENTATION_RIGHTBOTTOM:
            $image->flopImage();
            $image->rotateImage("#000", 90);
            break;
        case Imagick::ORIENTATION_LEFTBOTTOM:
            $image->rotateImage("#000", -90);
            break;
        default: // Invalid orientation
            break;
        }
        $image->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
        return $image;
    }

    private function resize($onlyThumbnail = false)
    {
        $params = array();
        $params['tmb_height'] = Yii::app()->params['tmb_height'];
        $params['tmb_width']  = Yii::app()->params['tmb_width'] ? Yii::app()->params['tmb_width'] : $params['tmb_height'];
        $params['max_image']  = Yii::app()->params['max_image_width'];

        $crop      = Yii::app()->settings->get('cms_settings', 'cropImages');
        $cropTop   = Yii::app()->settings->get('cms_settings', 'cropImagesTop');
        $watermark = Yii::app()->settings->get('cms_settings', 'watermark');

        foreach($this->uploaded as $item) {
            if ($item->type != 'image')
                continue;

            if (!is_file($item->src))
                continue;

            $imgInfo = getimagesize($item->src);
            if (!is_array($imgInfo))
                continue;

            $image = Yii::app()->image->load($item->src);

            if (!$onlyThumbnail && $image->width > $params['max_image']) {
                $image->resize($params['max_image'], $params['max_image'], 2)->quality(100);
                $image->save($item->src);
            }

            if ($watermark && isset(Yii::app()->params['watermark'])) {
                $this->createWatermark($item);
                if (!is_file($item->src)) {
                    throw new CException('Error creating watermark in '.$item->fname);
                }
            }

            if ($image->width > $params['tmb_height']) {
                if ($image->width > $image->height)
                    $masterSide = $crop ? Image::HEIGHT : Image::WIDTH;
                else
                    $masterSide = $crop ? Image::WIDTH : Image::HEIGHT;

                $image->resize($params['tmb_height'], $params['tmb_height'], $masterSide)->quality(100);
            }

            if ($crop)
                $image->crop($params['tmb_height'], $params['tmb_height'], $cropTop ? $cropTop : 'center');

            $image->save($item->path .DS. 'tmb_'.$item->fname);
        }
    }

    private function resizeOnParams($params)
    {
        if (!$this->uploaded) {
            return;
        }

        $params = $this->normalizeParams($params);

        foreach($this->uploaded as $item) {
            if ($item->type != 'image')
                continue;

            $image = Yii::app()->image->load($item->src);

            if (isset($params['master_side'])) {
                $masterSide = $params['master_side'];
            } else {
                $masterSide = $image->width > $image->height ? Image::HEIGHT : Image::WIDTH;
            }

            $image->resize($params['width'], $params['height'], $masterSide)->quality(100);

            if ($params['mode'] == self::MODE_CROP) {
                $image->crop($params['width'], $params['height'], $params['crop_top']);
            }

            $image->save($item->path .DS. 'tmb_'. $item->fname);
        }
    }

    private function normalizeParams($params)
    {
        $result = array();
        $result['width']  = false;
        $result['height'] = false;
        $result['mode']   = false;
        $result['crop_top'] = false;
        $result['master_side'] = false;

        if(isset($params['crop']))
        {
            $result['mode'] = self::MODE_CROP;

            if (isset($params['max']))
                $result['width'] = $result['height'] = $params['max'];

            if (isset($params['crop_top']))
                $result['crop_top'] = $params['crop_top'];
        } elseif (isset($params['width'])) {
            $result['width']  = $params['width'];
            $result['height'] = $params['height'];
        } else {
            $result['width'] = $result['height'] = $params['max'];
        }

        if (isset($params['master_side'])) {
            $result['master_side'] = $params['master_side'];
        }

        return $result;
    }
}
