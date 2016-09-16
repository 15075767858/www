<?php
if($_REQUEST["r"]){
echo file_get_contents("../../../userinfo.txt");
}
else{
$content=$_REQUEST["content"];
echo $content;
$myfile = fopen("/mnt/nandflash/userinfo.txt", "w") or die("Unable to open file!");
fwrite($myfile, $content);
fclose($myfile);
}
?>