<?php
$par=$_GET['par'];
$graphInstallPackageName="graphInstall";

if($par=="upload"){
	 echo move_uploaded_file($_FILES["file"]["tmp_name"], $dir . $_FILES["file"]["name"]);
}
if($par=="afterUpload"){
	$name=$_POST['nameArr'];
	$nameArr = json_decode($name);
	$names="";	
	foreach ($nameArr as $key => $value) {
		$names.=$value." ";
	}
	exec("cat ".$names."  >  ".$graphInstallPackageName);

	exec("tar -xzvf ".$graphInstallPackageName);
	listDir("/mnt/nandflash/");
    listDir("/var/www/");
	foreach ($arr as $key => $value) {
		echo $value;
		echo "<br>";
		unlink("./".$value);
	}
	unlink($graphInstallPackageName);

}

if($par=="install"){
	exec("tar -xzvf ".$graphInstallPackageName,$arr);
	echo $arr;
}

function listDir($dir)
{
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ((is_dir($dir . "/" . $file)) && $file != "." && $file != "..") {
                    listDir($dir . "/" . $file . "/");
                    chmod($dir.'/'.$file,0777);
                } else {
                    if ($file != "." && $file != "..") {
                        chmod($dir . '/' . $file, 0777);
                    }
                }
            }
            closedir($dh);
        }
    }
}
