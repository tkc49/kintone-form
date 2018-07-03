<?php

$basicAuthId = filter_input(INPUT_POST, 'basicAuthId');
$basicAuthPassword = filter_input(INPUT_POST, 'basicAuthPassword');
$appId = filter_input(INPUT_POST, 'appId');
$token = filter_input(INPUT_POST, 'token');


$json = array(
    array('name'=>'Google', 'url'=>$basicAuthId),
    array('name'=>'Yahoo!', 'url'=>'http://www.yahoo.co.jp/'),
);
 


header("Content-Type: text/javascript; charset=utf-8");
echo json_encode($json); // 配列をJSON形式に変換してくれる
exit();
?>