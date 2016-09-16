<?php
ini_set('date.timezone','Asia/Chongqing');
$sDate = $_REQUEST["date"];
    $sTime=$_REQUEST["time"];
echo "date -s  '".$sDate." ".$sTime."'";
$comm1 = "date -s  '".$sDate." ".$sTime."'";
exec($comm1,$array);
$comm2 = "hwclock -w";
exec($comm2,$array1);
?>
