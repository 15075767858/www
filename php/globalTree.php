<?php
$ip = $_GET['ip'];
$port = $_GET['port'];


$ips = explode(',',$ip);



$resArr=array();

foreach($ips as $value){

    $res= getTreeData($value,$port);
    if($res){
    array_push($resArr,$res);
    }
}


echo json_encode($resArr);




function getTreeData($ip, $port)
{

    $redis = new Redis();
    $redis->connect($ip, $port,0.5) or $redis=false;
    if(!$redis){
        return false;
    }
    $arList = $redis->keys("???????");
    sort($arList);
    $devs = array();
    foreach ($arList as $key => $value) {
        array_push($devs, substr($value, 0, 4));
    }
    $devs = array_unique($devs);
    $resArr = array();
    foreach ($devs as $key => $value) {
        $arr = array();
        $arr['text'] = $value;
        $arr['leaf'] = false;
        $arr['checked'] = false;
        $arr['children'] = getDevNodeTypeByDevName($value, $redis);
        //$arList = $redis->keys("???????");
        array_push($resArr, $arr);
    }

    $arr1=array();
    $arr1['text'] = $ip;
    $arr1['leaf'] = false;
    //$arr['checked'] = false;
    $arr1['children']=$resArr;
    return $arr1;

}

function getDevNodeTypeByDevName($devName, $redis)
{
    $arr = array();
    $types = array('AI' => '0??', 'AO' => '1??', "AV" => "2??", "BI" => "3??", "BO" => "4??", "BV" => "5??");
    foreach ($types as $key => $value) {
        $arr1 = array();
        $arr1['text'] = $key;
        $key = $devName . $value;

        //echo $key;
        $keys = $redis->keys($key);
        sort($keys);

        $arr1['children'] = keysToNodeName($keys);
        $arr1['checked'] = false;
        if (count($keys) > 0) {
            array_push($arr, $arr1);

        }
    }
    return $arr;
}

function keysToNodeName($arr)
{
    $nodeNameArr = array();
    foreach ($arr as $key => $value) {
        $tempArr = array();
        $tempArr["text"] = $value;
        $tempArr["leaf"] = true;
        $tempArr['checked'] = false;
        array_push($nodeNameArr, $tempArr);
    }
    return $nodeNameArr;
}


?>