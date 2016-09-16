<meta charset="utf-8">
<?php 
$sources='initfiles';
$directory="../../../";
$newArry=Array();
$scanned_directory=array_diff(scandir($sources),array('..','.'));
foreach ($scanned_directory as $key => $value) {
	$kuozhanming= strpbrk($value,".");
	echo "初始化 <b style='color:red'>".substr($value,0,stripos($value, '.'))."</b> Ok";
	echo "<br>";
	copy($sources."/".$value,$directory."/".$value);
}
echo "<br>";
echo "全部文件初始化完成。";
?>