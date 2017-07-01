<?php
$par = $_REQUEST['par'];
$mysqlConfigXmlPath="/mnt/nandflash/mysqlconfig.xml";
if ($par == "testConnect") {
    $mysql = mysqli_connect($_REQUEST['host'], $_REQUEST['username'], $_REQUEST['password']);
    if ($mysql) {
        echo "<div style='color: green;'>database check success .</div>";
    } else {
        echo "<div style='color: red;'>database check failure .</div>";
    }
    $result = mysqli_query($mysql, "use " . $_REQUEST['databasename']);
    if ($result) {
        echo "<div style='color: green;'>database  link success .</div>";
    } else {
        echo "<div style='color: red;'>database  link failure .</div>";
    }
}

if ($par == "createTable") {
    $mysql = mysqli_connect($_REQUEST['host'], $_REQUEST['username'], $_REQUEST['password']);
    $result = mysqli_query($mysql, "CREATE DATABASE IF NOT EXISTS `" . $_REQUEST['databasename'] . "`;");
    if ($result) {
        echo "<div style='color: green;'>database  create success .</div>";
    }
    $result = mysqli_select_db($mysql, $_REQUEST["databasename"]);
    $fileName = "../resources/deviceip.sql";
    $_sql = file_get_contents($fileName);
    $_arr = explode(';', $_sql);
    foreach ($_arr as $_value) {
        $result = $mysql->query($_value . ';');
    }
    echo "<div style='color: green;'>database table create success .</div>";
}
if ($par == "saveConfig") {
    $xmlstr = '<?xml version="1.0" encoding="UTF-8" ?>
<root>
    <host>' . $_REQUEST["host"] . '</host>
    <username>' . $_REQUEST["username"] . '</username>
    <password>' . $_REQUEST["password"] . '</password>
    <databasename>' . $_REQUEST["databasename"] . '</databasename>
</root>';
    echo "ok " . file_put_contents($mysqlConfigXmlPath, $xmlstr);
}
if ($par == "getConfig") {
    echo file_get_contents($mysqlConfigXmlPath);
}

if($par=="runListen"){
    
   $pPath =  substr(__DIR__,0,strlen(__DIR__)-3);
    echo system($pPath."resources/images/util/packageWindows/n.exe". $pPath."resources/images/util/packageWindows/index.js");
}