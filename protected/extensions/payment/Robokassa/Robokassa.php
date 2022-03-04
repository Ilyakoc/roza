<?php
/**
 * Created by JetBrains PhpStorm.
 * User: AlexOk
 * Date: 05.11.12
 * Time: 23:01
 * To change this template use File | Settings | File Templates.
 */
class Robokassa
{
    /**
     * @param $price
     * @param $order_id
     * @param $login
     * @param $password
     * @return string
     */
    public static function generateSignature($price, $order_id, $login, $password)
    {
        $params = array(
            $login,
            $price,
            $order_id,
            $password
        );
        return md5(implode(':', $params));
    }

    /**
     * @param $signature
     * @param $sum
     * @param $invid
     * @param $password
     * @return bool
     */
    public static function compareSignature($signature, $sum, $invid, $password)
    {
        $params = array(
            $sum,
            $invid,
            $password
        );

        return strtolower($signature) === md5(implode(':', $params));
    }
}
