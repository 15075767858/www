<?php
$ip = $_SERVER["SERVER_ADDR"];
$redis = new Redis();
$redis->connect($ip, 6379);

$key = $_POST["key"];
$value = $_POST["value"];
echo $redis->publish($key, $value);
