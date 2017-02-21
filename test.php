<?php
exec("uname -a ",$arr);
if (strstr($arr[0], "linux")) {
        return true;
    } else {
        return false;
 }

/*$zip =new ZipArchive();
print_r($zip);*/