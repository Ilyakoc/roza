<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 30.11.11
 * Time: 10:47
 * To change this template use File | Settings | File Templates.
 */
class UserAuth extends CUserIdentity
{
    public function authenticate()
    {
        if (!function_exists('curl_init') || isset(Yii::app()->params['localauth'])) {
            return $this->localAuth();
        } else {
            return $this->extAuth();
        }
    }

    private function localAuth()
    {
        $users = include(Yii::getPathOfAlias('application.config').DS.'users.php');

        if (!isset($users[$this->username]))
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        else if($users[$this->username]!==$this->password)
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        else
            $this->errorCode=self::ERROR_NONE;
        return !$this->errorCode;
    }

    private function extAuth()
    {
        if (!function_exists('curl_init')) {
            throw new CException('Curl not found');
        }

        $data = array(
            'username'=>$this->username,
            'password'=>$this->password,
            'domain'=>Yii::app()->request->serverName
        );

        $authServer = Yii::app()->params['authServer'] ?
            Yii::app()->params['authServer'] : 'http://login.dishman.ru';

        $curl = curl_init($authServer);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_REFERER, 'http://'. $data['domain']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $result = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($result);
        $this->errorCode = $result ? $result->errorCode : 1;
        return !$this->errorCode;
    }
}
