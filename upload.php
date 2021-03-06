<?php

$par = $_REQUEST['par'];
$graphInstallPackageName = "graphInstall";
if ($par == "upload") {
    //echo move_uploaded_file($_FILES["file"]["tmp_name"], __dir__ . $_FILES["file"]["name"]);
    $filename = $_FILES["file"]["name"];
    $str1 = "mv " . $_FILES["file"]["tmp_name"] . " " . __dir__ . "/" . $filename;
    $str2 = "chmod 777 " . $filename;
    $str3 = "tar -xzvf " . $filename;
    $str4 = "chmod 777 * -R";
    echo $str1;
    echo $str2;
    echo $str3;
    echo $str4;
    excuteTelnetCommand(array($str1, $str2, $str3, $str4));
}

function isWindows()
{
    return PHP_OS == "WINNT";
}

function isMac()
{
    return PHP_OS == "Darwin";
}

function isUbuntu()
{
    exec('uname -a', $arr);
    $str = strtolower($arr[0]);
    if (strstr($str, "ubuntu")) {
        return true;
    } else {
        return false;
    }
}

function isLinux()
{
    exec('uname -a', $arr);
    $str = strtolower($arr[0]);
    if (strstr($str, "linux")) {
        return true;
    } else {
        return false;
    }
}

function removeFile($filename)
{
    $str = 'rm -rf ' . $filename;
    echo exec($str);
}


if ($par == 'beforeUploadBigFile') {
    $filename = $_REQUEST['fileName'];
    if (isWindows()) {
        unlink($filename);
    }
    removeFile($filename);
}

if ($par == 'uploadFodlerWWW') {
    echo file_put_contents($_REQUEST['fileName'], file_get_contents($_FILES['file']['tmp_name']), FILE_APPEND);
}

if ($par == 'installPackage') {
    installPackage($_REQUEST['fileName']);
}
if ($par == "test") {

    $fp = @popen("/bin/su", "r");
    @fputs($fp, "123456");

    //@fputs($fp, "chmod 777 -R *");
    print_r(@fread($fp, 2016));
    @pclose($fp);

}

function installPackage($fileName)
{
    set_time_limit(0);
    if (isWindows()) {
        $zip = new ZipArchive();
        if ($zip->open($fileName) === TRUE) {
            $zip->extractTo('./');
            $zip->close();
        }
        //zip_flatten($fileName, './');
        echo " ok ";
        exit();
    }
    if (isUbuntu()) {
        exec("su \r\n123456\r\nchmod 777 * -R\r\ntar xzvf " . $fileName, $arr1);
        exec("su \r\n123456\r\nchmod 777 * -R");
        exec("su \r\n123456\r\nfind ./ -name ._* | xargs rm -rf");

        //exec("su \r\n123456\r\ntar -xzvf " . $fileName . "\r\n chmod -R  777 *\r\n", $arr1);
        echo json_encode($arr1);
    } else {
        chmod($fileName, 777);
        exec("tar -xzvf " . $fileName, $arr1);
        exec("find ./ -name ._* | xargs rm -rf");
        echo json_encode($arr1);
    }
}

function zip_flatten($zipfile, $dest = '.')
{
    $zip = new ZipArchive;
    if ($zip->open($zipfile)) {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            if (substr($entry, -1) == '/') continue; // skip directories

            $fp = $zip->getStream($entry);
            $ofp = fopen($dest . '/' . basename($entry), 'w');

            if (!$fp)
                throw new Exception('Unable to extract the file.');

            while (!feof($fp))
                fwrite($ofp, fread($fp, 8192));

            fclose($fp);
            fclose($ofp);
        }

        $zip->close();
    } else
        return false;

    return $zip;
}

if ($par == 'uname') {
    exec('uname -a', $arr);
    if (strstr($arr[0], "Ubuntu")) {

    } else {
        echo "no ubuntu";
    }
//    echo json_encode($arr);
}
if ($par == 'uploadAppendFile') {

    echo file_put_contents('../' . $_REQUEST['fileName'], file_get_contents($_FILES['file']['tmp_name']), FILE_APPEND);
}

if ($par == 'beforeUploadGraph') {
    listDir();
}

if ($par == "afterUpload") {
    $name = $_POST['nameArr'];
    $nameArr = json_decode($name);
    $names = "";
    foreach ($nameArr as $key => $value) {
        $names .= $value . " ";
    }
    exec("cat " . $names . "  >  " . $graphInstallPackageName);

    exec("tar -xzvf " . $graphInstallPackageName);
    foreach ($arr as $key => $value) {
        echo $value;
        echo "<br>";
        unlink("./" . $value);
    }
    unlink($graphInstallPackageName);
}

if ($par == "updateProgram") {
    $fn = $_REQUEST['filename'];
    uploadProgram($fn);
}

if ($par == "install") {
    exec("tar -xzvf " . $graphInstallPackageName, $arr);
    echo $arr;
}

if ($par == "system") {
    $str = $_REQUEST['command'];
    exec($str, $arr);
    echo json_encode($arr);
}
/*
modbus.sh最前面。执行完了以后30秒bac-logic-modbus，再延时30秒bac-server-modbus
*/
function listDir()
{


    $dir = __DIR__;
    $telnet = getTelnet();

    $telnet->write("cd $dir\r\n");

    $telnet->write("chmod 777 *\r\n");

    echo $telnet->read_till(":> ");
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ((is_dir($dir . "/" . $file)) && $file != "." && $file != "..") {
                    $path = $dir . "/" . $file;
                    $telnet->write("chmod 777 $path -R\r\n");
                    echo $path;
                    echo $telnet->read_till(":> ");
                    echo "<br>";
                } else {
                    if ($file != "." && $file != "..") {
                        //chmod($dir . '/' . $file, 0777);
                    }
                }
            }
            closedir($dh);
        }
    }

    echo $telnet->close();

}


function excuteTelnetCommand($commandArr = false)
{
    $dir = __DIR__;
    $telnet = getTelnet();
    $telnet->write("cd " . $dir . "\r\n");
    $telnet->write("chmod 777 *\r\n");
    $telnet->write("pwd\r\n");
    if ($commandArr) {
        foreach ($commandArr as $key => $value) {
            $telnet->write($value . "\r\n");
            echo $telnet->read_till(":> ");//拿到执行结果.
        }
    }

    $telnet->write("chmod 777 *\r\n");
    echo $telnet->read_till(":> ");//拿到执行结果.
    echo $telnet->close();

    exit();

    //   $telnet->write($command."\r\n");

}

if ($par == "uploadProgram") {
    $name = $_REQUEST['filename'];
    uploadProgram($name);
}

function uploadProgram($name)
{
    $telnet = getTelnet();
    $dir = __DIR__;
    $telnet->write("cd $dir\r\n");
    $telnet->write("pwd\r\n");
    echo $telnet->read_till(":> ");
    echo "<br>";
    $telnet->write("tar xzvf $name graph\r\n");
    echo $telnet->read_till(":> ");
    echo $telnet->close();
}

function getTelnet()
{

    include('telnet.php');
    //error_reporting(-1);
    $telnetUP = simplexml_load_file('telnet.xml') or $telnetUP = false;
    if (!$telnetUP) {
        return;
    }
    $username = $telnetUP->username;
    $password = $telnetUP->password;
    $rootpw = $telnetUP->root;
    $telnet = new telnet('127.0.0.1', 23);

    echo $telnet->read_till("login: ");
    $telnet->write("$username\r\n");
    echo $telnet->read_till("password: ");
    $telnet->write("$password\r\n");
    $telnet->write("su\r\n");
    echo $telnet->read_till("password: ");
    $telnet->write("$rootpw\r\n");
    $telnet->write("pwd\r\n");
    echo $telnet->read_till(":> ");//拿到执行结果.
    return $telnet;
}
