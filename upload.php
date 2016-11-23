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

function removeFile($filename)
{
    $str = 'rm -rf ' . $filename;
    echo exec($str);
}


if ($par == 'beforeUploadBigFile') {
    $filename = $_REQUEST['fileName'];
    removeFile($filename);
}

if($par=='uploadFodlerWWW'){
    echo file_put_contents($_REQUEST['fileName'], file_get_contents($_FILES['file']['tmp_name']), FILE_APPEND);
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

if ($par == 'test') {
    excuteTelnetCommand(array(
        'mkdir aaa', 'mkdir bbb'
    ));
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
