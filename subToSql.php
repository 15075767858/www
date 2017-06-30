<?php

$redis = new Redis();
$redis->connect("127.0.0.1", "6379",10);
exit();
$redis->psubscribe(["127.0.0.1","*"], "callback1");
$redis = new Redis();
$redis->connect("192.168.31.188", "6379",10);
$redis->psubscribe(["192.168.31.188","*"], "callback1");
function callback1($redis, $channel, $message, $val)
{
    print_r($redis);
    echo "<br>";
    print_r($channel);
    echo "<br>";
    print_r($message);
    echo "<br>";
    print_r($val);

    //exit();
}


exit();
//ini_set('default_socket_timeout', -1);
//error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
ini_set('default_socket_timeout', -1);
$timeout = $_REQUEST['redistimeout'];
if($timeout>10){
    $timeout=10;
}

$linkIp = $_REQUEST['ip']||"127.0.0.1";
$redis = null;

try {
    $redis = new Redis();
    if (!$redis) {
        echo json_encode(array('success' => false, 'info' => $linkIp . ' Redis error'));
        exit();
    }
    $redis->connect($linkIp, "6379", $timeout) or $redis = false;
    if (!$redis) {
        echo json_encode(array('success' => false, 'info' => $linkIp . 'Connction error'));
        exit();
    }
} catch (Exception $e) {
    echo json_encode(array('success' => false, 'info' => $linkIp . 'Connction error' . $e));
    exit();
}


//$channel =$_REQUEST['subnode'];  // channel

$channels = json_decode($_REQUEST['subnodes']);
subscribe($redis, $_REQUEST['ip']);
function subscribe($redis, $linkIp)
{
    try {
        $redis->psubscribe(json_decode($_REQUEST['subnodes']), "callback");
    } catch (Exception $e) {
        echo json_encode(array("success" => false, 'info' => $linkIp . $e));
    }
}

function callback($redis, $channel, $message, $val)
{
    $types = json_decode($_REQUEST['types']);
    foreach ($types as $type) {
        if (strstr($val, $type)) {
            $ip = $_REQUEST['ip'];
            $arr = array();
            $arr['ip'] = $ip;
            $arr['time'] = $_REQUEST['time'];
            if (preg_match("/[\x7f-\xff]/", $val)) {  //判断字符串中是否有中文
                $arr['value'] = "base64=" . base64_encode($val);
            } else {
                $arr['value'] = $val;
            }
            echo json_encode(array("success" => true, 'info' => $arr));
            exit;
        }
    }
    subscribe($redis, $_REQUEST['ip']);
}

