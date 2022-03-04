<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alexok
 * Date: 01.06.11
 * Time: 15:27
 */

class SettingsForm extends CFormModel
{
    public static $files = array('file_top_button');
    /**
     * @var array имена файлов. В формате array(attribute=>name),
     * где attribute - имя атрибута файла из SettingsForm::$files,
     * name - произвольное имя файла, без расширения.
     */
    public static $filesNames = array();
    
    public $slogan;
    public $address;
    public $sitename;
    public $phone_code;
    public $phone;
    public $email;
    public $firm_name;
    public $counter;
    public $hide_news;
    public $menu_limit;
    public $cropImages;
    public $comments;
    public $meta_title;
    public $meta_key;
    public $meta_desc;
    public $watermark;
    public $blog_show_created;
    public $file_top_button;

    public $vk;
    public $instagram;

    public $quality_text;
    public $return_text;
    public $delivery_text;

    public $delivery_price1;
    public $delivery_price2;
    public $delivery_price3;

    public $delivery_text1;
    public $delivery_text2;
    public $delivery_text3;

    public $away_area_map;
    public $away_area_text;

    public $header_link;
    public $header_link_text;

    public function rules()
    {
        return array(
            array('sitename', 'length', 'max'=>40),
            array('slogan, address, sitename, phone_code, phone, email, firm_name, counter, hide_news, menu_limit, '.
                'comments, meta_title, meta_key, meta_desc, cropImages, watermark, blog_show_created, header_link, header_link_text', 'safe'),

            ['quality_text, return_text, delivery_text, delivery_price1, delivery_price2, delivery_price3, delivery_text1, delivery_text2, delivery_text3, vk, instagram', 'safe'],
            ['away_area_text, away_area_map, file_top_button', 'safe'],
        );
    }

    public function attributeLabels()
    {
        return array(
            'file_top_button' => 'Изображение для кнопки',
            'slogan'=>'Слоган сайта',
            'address'=>'Контактные данные',
            'sitename'=>'Название сайта',
            'phone'=>'Телефон',
            'phone_code'=>'Код города',
            'email'=>'Email администратора',
            'firm_name'=>'Название организации',
            'counter'=>'Счетчики',
            'hide_news'=>'Скрыть новости',
            'menu_limit'=>'Кол-во пунктов меню',
            'cropImages'=>'Обрезка изображений',
            'comments'=>'Код комментариев',
            'meta_title'=>'SEO заголовок',
            'meta_key'=>'Ключевые слова',
            'meta_desc'=>'Описание',
            'watermark'=>'Водяной знак',
            'blog_show_created'=>'Показывать дату создания',

            'quality_text' => 'Гарантия качества',
            'return_text' => 'Условия возврата',
            'delivery_text' => 'Подробнее о доставке',

            'delivery_price1' => 'Цена доставки #1',
            'delivery_price2' => 'Цена доставки #2',
            'delivery_price3' => 'Цена доставки #3',

            'delivery_text1' => 'Районы доставки #1',
            'delivery_text2' => 'Районы доставки #2',
            'delivery_text3' => 'Районы доставки #3',

            'away_area_map' => 'Код карты (отдаленные районы)',
            'away_area_text' => 'Текст (отдаленные районы)',

            'instagram' => 'Ссылка на Instagram',
            'vk' => 'Ссылка на VK',

            'header_link' => 'Ссылка в меню',
            'header_link_text' => 'Ссылка в меню (текст)',
        );
    }

    public function saveSettings()
    {
        // save files
        $uploadPath = Yii::getPathOfAlias('webroot') . Yii::app()->params['uploadSettingsPath'];

        foreach($this::$files as $fileAttribute) {
            $oldFile = Yii::app()->request->getPost($fileAttribute . '_file');

            $this->{$fileAttribute} = CUploadedFile::getInstance($this, $fileAttribute);

            if(is_object($this->{$fileAttribute})) {
                if(isset(self::$filesNames[$fileAttribute])) {
                    $filePreviewName=self::$filesNames[$fileAttribute];
                }
                else {
                    $filePreviewName=uniqid();
                }
                $previewName = $filePreviewName . '.' . $this->{$fileAttribute}->extensionName;

                $this->{$fileAttribute}->saveAs($uploadPath . $previewName);
                $this->{$fileAttribute} = $previewName;

                if(!empty($oldFile) && ($oldFile != $previewName)) {
                    $delete = $uploadPath . DS . $oldFile;
                    if(file_exists($delete)) unlink($delete);
                }
            }

            if(empty($this->{$fileAttribute}) && !empty($oldFile)) $this->{$fileAttribute} = $oldFile;
        }

        Yii::app()->settings->set('cms_settings', $this->attributes);
    }

    public function loadSettings()
    {
        foreach($this->attributeNames() as $attr) {
            $this->$attr = Yii::app()->settings->get('cms_settings', $attr);
        }
    }
}
 
