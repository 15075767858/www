<?php
$ip=$_GET['ip'];
$port=$_GET['port'];
$redis = new Redis();

/*if ($ip == "127.0.0.1") {
    $redis->connect("192.168.253.253", 6379);
} else {
	}*/
    $redis->connect($ip, $port);



//$arList = $redis->keys("???????");
$arList = $redis->keys("???????");

sort($arList);
$devs=array();

foreach ($arList as $key => $value) {
	array_push($devs, substr($value,0,4));
}
$devs= array_unique($devs);
$resArr=array();
foreach ($devs as $key => $value) {
	$arr=array();
	$arr['text']=$value;
	$arr['leaf'] = false;
	$arr['checked'] = false;
	$arr['children']=getDevNodeTypeByDevName($value,$redis);
	//$arList = $redis->keys("???????");
	array_push($resArr, $arr);
}

function getDevNodeTypeByDevName($devName,$redis){
	$arr=array();
	$types=array('AI'=>'0??','AO'=>'1??',"AV"=>"2??","BI"=>"3??","BO"=>"4??","BV"=>"5??");
	foreach ($types as $key => $value) {
		$arr1=array();
		$arr1['text']=$key;
		$key=$devName.$value;

		//echo $key;
		$keys = $redis->keys($key);
		sort($keys);

		$arr1['children']=keysToNodeName($keys);
$arr1['checked'] = false;
		if(count($keys)>0){
			array_push($arr, $arr1);

		}
	}
	return $arr;
}
function keysToNodeName($arr){
	$nodeNameArr=array();
		foreach ($arr as $key => $value) {
			$tempArr=array();
			$tempArr["text"]=$value;
			$tempArr["leaf"]=true;
$tempArr['checked'] = false;
			array_push($nodeNameArr, $tempArr);
		}
		return $nodeNameArr;
}

echo json_encode($resArr);

?>