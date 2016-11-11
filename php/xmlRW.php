<?php  
$fn=$_REQUEST['fileName'];
$rw=$_REQUEST['rw'];
$fn="../".$fn;
if($rw=='r'){
	if(!file_exists($fn)){
		return;
	}
    $fp = fopen($fn, 'r');
    while(! feof($fp))
{ 
echo fgets($fp); 
} 
}else{
$content=$_REQUEST["content"];
    $fp = fopen($fn, 'w') or die("Unable to open file!");

    if(isset($_REQUEST['callback'])){
        echo "save success ";
    }
    echo fwrite($fp, $content);
fclose($fp);
}
?>