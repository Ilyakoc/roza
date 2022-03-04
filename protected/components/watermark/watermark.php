<?php

    class Watermark {
        public $fontSize = 30;

        private $filename;
        private $color = 'black';
        public function __construct($filename) {
            $this->filename = $filename;
        }

        public function getWatermark() {
            $image = new Imagick($this->filename);
            $draw  = new ImagickDraw();
            $pixel = new ImagickPixel('transparent');

            $dimensions = $image->getImageGeometry();

            $width = $dimensions['width'];
            $height = $dimensions['height'];

            $dimensionsWidthValidate  = isset(Yii::app()->params['watermark']['minwidth'])  ? (Yii::app()->params['watermark']['minwidth']  < $width) : true;
            $dimensionsHeightValidate = isset(Yii::app()->params['watermark']['minheight']) ? (Yii::app()->params['watermark']['minheight'] < $height) : true;

            if($dimensionsWidthValidate && $dimensionsHeightValidate) {
                $a2 = pow($width, 2);
                $b2 = pow($height, 2);
                $hypotenuse = sqrt($a2 + $b2 - 2 * $width * $height * cos(deg2rad(90)));
                $angle = rad2deg( acos( ($b2 + pow($hypotenuse, 2) - $a2) / (2 * $height * $hypotenuse) ) );

                if($height > $width) {

                    $angle = 90 - $angle;

                }

                $pixel->setColor($this->color);
                $draw->setFontSize( (int) $this->fontSize );
                $draw->setFillColor( "rgba(0,0,0,0.2)" );
                $image->annotateImage($draw, 0, 0, $angle, str_repeat(' watermark ', 50));
                $image->writeImage();
            }
        }
    }