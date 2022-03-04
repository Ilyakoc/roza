<?php
//$protocol=$_SERVER['PROTOCOL'] = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http';
//$host=$protocol.'://'.$_SERVER['SERVER_NAME'].'/';
$page = 'https://bazaroza.ru/sitemap/';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $page);
curl_setopt($ch, CURLOPT_HEADER, 0);
$returned = curl_exec($ch);

curl_close($ch);
?>