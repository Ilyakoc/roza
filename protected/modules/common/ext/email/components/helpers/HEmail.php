<?php
/**
 * Класс-помощник для расширения common\ext\email
 * 
 * Использует библиотеку PHPMailer (/common/vendors/PHPMailer/src/PHPMailer.php)
 * @link https://github.com/PHPMailer/PHPMailer
 */
namespace common\ext\email\components\helpers;

require_once(\Yii::getPathOfAlias('common.vendors.PHPMailer.src').'/PHPMailer.php');
require_once(\Yii::getPathOfAlias('common.vendors.PHPMailer.src').'/Exception.php');
require_once(\Yii::getPathOfAlias('common.vendors.PHPMailer.src').'/SMTP.php');

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class HEmail
{
    /**
     * Отправка сообщения
     * @param array $config
     * "debug"=>boolean
     * "language"=>"ru"
     * "smtp"=>[
     *      "SMTPDebug"=>
     *      "Host"=>
     *      "SMTPAuth"=>
     *      "Username"=>
     *      "Password"=>
     *      "SMTPSecure"=>
     *      "Port"=>
     * ]
     * "address"=>[
     *      "from"=>email|[email, name],
     *      "to"=>[email, [email, name], ...],
     *      "reply"=>[email, [email, name], ...],
     *      "cc"=>[email, [email, name], ...],
     *      "bcc"=>[email, [email, name], ...],
     * ]
     * "attachment"=>[filename, [filename, optionalname], ...]
     * "ishtml"=>boolean,
     * "subject"=>string
     * 
     * @param string|mixed $data
     * @param string|false $bodyView шаблон отображения тела письма
     * @param string|false $altBodyView шаблон отображения альтернативной 
     * версии тела письма
     * 
     * @return bool
     */
    public static function send($config, $data, $bodyView=false, $altBodyView=false)
    { 
        $mail=new \PHPMailer\PHPMailer\PHPMailer(A::get($config, 'debug', false));
        try {
            if($debug=A::get($config, 'debug')) {
                $mail->SMTPDebug=is_numeric($debug) ?: 2;
            }
            
            if($language=A::get($config, 'language', 'en')) {
                $mail->setLanguage($language, \Yii::getPathOfAlias('common.vendors.PHPMailer.language').DIRECTORY_SEPARATOR);
            }
            if($charset=A::get($config, 'charset', 'utf-8')) {
                $mail->CharSet=$charset;
            }
            
            static::setSMTP(A::get($config, 'smtp'));            
            static::setFrom($mail, A::rget($config, 'address.from', 'info@'.$_SERVER['SERVER_NAME']));
            
            foreach(['to'=>'addAddress', 'reply'=>'addReplyTo', 'cc'=>'addCC', 'bcc'=>'addBCC'] as $param=>$method) {
                static::addAddresses($mail, A::rget($config, 'address.'.$param, []), $method);
            }
            
            static::addAttachments($mail, A::rget($config, 'address.attachment', []));
        
            $mail->isHTML(A::get($config, 'ishtml', ($bodyView ? true : false)));
            $mail->Subject=A::get($config, 'subject', '');
            
            if($bodyView) {
                $mail->Body=Y::controller()->renderPartial($bodyView, $data, true);
            }
            else {
                $mail->Body=$data;
            }
            
            if($altBodyView) {
                $mail->AltBody=Y::controller()->renderPartial($altBodyView, $data, true);
            }
            else {
                $mail->AltBody=strip_tags(preg_replace('#<br[ /]*?>#i', "\n", $mail->Body));
            }
            
            return $mail->send();
        }
        catch (\PHPMailer\PHPMailer\Exception $e) {
            $message='Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
            \Yii::log($message, 'error');
            return false;
        }
        
        return true;
    }
    
    public static function cmsSend($to, $subject, $data, $bodyView=false, $altBodyView=false)
    {
        $config=static::config();
        $config['address']['to']=[static::normalizeAddress(A::toa($to))];
        $config['ishtml']=true;
        $config['subject']=$subject;
        
        return static::send($config, $data, $bodyView, $altBodyView);
    }
    
    public static function cmsAdminSend($subject, $data, $bodyView, $altBodyView)
    {
        if($subject === true) {
            $subject='Новое сообщение с сайта ' . \D::cms('sitename', $_SERVER['SERVER_NAME']);
        }
        $config=static::config();
        $config['address']['to']=[static::normalizeAddress(A::toa(\D::cms('email')))];
        $config['ishtml']=true;
        $config['subject']=$subject;
        
        return static::send($config, $data, $bodyView, $altBodyView);
    }
    
    /**
     * Установить параметры SMTP
     * @param \PHPMailer\PHPMailer\PHPMailer &$mail
     * @param array $smtp настройки для SMTP
     */
    public static function setSMTP(&$mail, $smtp)
    {
        if($smtp) {
            $mail->isSMTP();
            foreach(['Host', 'SMTPAuth', 'Username', 'Password', 'SMTPSecure', 'Port'] as $param) {
                if(A::exists($smtp, $param)) {
                    $mail->{$param}=A::get($smtp, $param);
                }
            }
        }
    }
    
    /**
     * Установить адрес отправителя
     * @param \PHPMailer\PHPMailer\PHPMailer &$mail
     * @param array|string $from адрес отправителя
     */
    public static function setFrom(&$mail, $from)
    {
        if($from=static::normalizeAddress($from)) {
            if(is_array($from)) $mail->setFrom($from[0], $from[1]);
            else $mail->setFrom($from);
        }
    }
    
    /**
     * Добавить адрес
     * @param \PHPMailer\PHPMailer\PHPMailer &$mail
     * @param array $addresses массив адресов
     * @param string $method имя метода добавления, по умолчанию "addAddress"
     */
    public static function addAddresses(&$mail, $addresses, $method='addAddress')
    {
        foreach($addresses as $address) {
            if($address=static::normalizeAddress($address)) {
                if(is_array($address)) $mail->$method($address[0], $address[1]);
                else $mail->$method($address);
            }
        }
    }
    
    /**
     * Прикрпить файлы
     * @param \PHPMailer\PHPMailer\PHPMailer &$mail
     * @param array $attachments массив прикрепляемых файлов
     */
    public static function addAttachments(&$mail, $attachments)
    {
        foreach($attachments as $attachment) {
            if($attachment=static::normalizeAttachment($attachment)) {
                if(is_array($attachment)) $mail->addAttachment($attachment[0], $attachment[1]);
                else $mail->addAttachment($attachment);
            }
        }
    }
    
    /**
     * Нормализовать почтовый электронный адрес
     * @param mixed $address почтовый электронный адрес
     * @return array|string|false  [email, name] | email | false.
     */
    public static function normalizeAddress($address)
    {
        if($address) {
            if(is_array($address)) {
                $email=array_shift($address);
                if(empty($address)) {
                    return $email;
                }
                $name=array_shift($address);
                return [$email, $name];
            }
            else {
                return $address;
            }
        }
        
        return false;
    }
    
    /**
     * Нормализовать прикрепляемый файл
     * @param mixed $address прикрепляемый файл
     * @return array|string|false  [filename, name] | filename | false.
     */
    public static function normalizeAttachment($attachment)
    {
        if($attachment) {
            if(is_array($attachment)) {
                $filename=array_shift($attachment);
                if(empty($attachment)) {
                    return $filename;
                }
                $name=array_shift($attachment);
                return [$filename, $name];
            }
            else {
                return $attachment;
            }
        }
        
        return false;
    }
    
    /**
     * Получение конфигурации для PHPMailer заданной по умолчанию
     * @return array
     */
    public static function config()
    {
        /*
        [
            'language'=>'ru',
            'charset'=>'utf-8',
            'debug'=>
            'smtp'=>[
                'Host'=>
                'Port'=>
                'SMTPSecure'=>
                'SMTPAuth'=>
                'Username'=>
                'Password'=>
            ],
            'address'=>[
                'from'=>[email, name]
                'from'=>email
                'to'=>
                'reply'=>[
                    email,
                    [email, name]
                    ...
                ],
                'cc'=>
                'bcc'=>
            ]
        ];
        /**/
        return Y::param('common.ext.email', []);
    }
}
