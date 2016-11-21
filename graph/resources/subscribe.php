<?php


//ini_set('default_socket_timeout', -1);

ini_set('default_socket_timeout', -1);
$timeout = 15;
$linkIp = $_REQUEST['ip'];
$redis = null;

try {
    $redis = new Redis();
    if (!$redis) {
        echo json_encode(array('success' => false, 'info' => $linkIp . ' Redis error'));
        exit();
    }
    $redis->connect($_REQUEST['ip'], "6379", $timeout) or $redis = false;
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
try {
    $redis->psubscribe($channels, "callback");
} catch (Exception $e) {
    echo json_encode(array("success" => false, 'info' => $linkIp . " Timeout " . $timeout . $e));

}

function callback($redis, $channel, $message, $val)
{
    //$ip = $_SERVER["SERVER_ADDR"];
    $ip = $_REQUEST['ip'];
    $arr = array();
    $arr['ip'] = $ip;

    if (preg_match("/[\x7f-\xff]/", $val)) {  //判断字符串中是否有中文
        $arr['value'] = "base64=" . base64_encode($val);
    } else {
        $arr['value'] = $val;
    }

    //if (!empty($_REQUEST['callback'])) {
    //    header('Content-Type: application/javascript');
    //    //echo $_REQUEST['callback'] . '(';
    //    echo $_REQUEST['callback'] . '(';
    //}

    echo json_encode(array("success" => true, 'info' => $arr));

    //if (!empty($_REQUEST['callback'])) {
    //    echo ');';
    //}

    exit;
}

