<?php  
$fn=$_POST['fileName'];
$rw=$_POST['rw'];
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
$content=$_POST["content"];
    $fp = fopen($fn, 'w') or die("Unable to open file!");
    
    fwrite($fp, $content);
fclose($fp);
}
?>