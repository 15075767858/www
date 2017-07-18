<?php

$ip = $_SERVER["SERVER_ADDR"];
$par = $_REQUEST["par"];
/*$redis = new Redis();
if ($ip == "127.0.0.1") {
    $redis->connect("192.168.253.253", 6379);
} else {
    $redis->connect($ip, 6379);
}*/

//echo move_uploaded_file($_FILES["file"]["tmp_name"], "devsinfo/" . $_FILES["file"]["name"]);

if ($par == "uploadHomeFile") {
    echo move_uploaded_file($_FILES["file"]["tmp_name"], "../../home/" . $_FILES["file"]["name"]);
}

if ($par == 'getSvgTree') {
    //$path = "svg";
    $path = $_REQUEST['path'];
    //$path = "SvgHvac";
    echo json_encode(getfiles($path, $fileArr = Array()));
}


if ($par == "getdevs") {
    $redis = getRedisConect();
    if ($redis) {
        $arList = $redis->keys("???????");
        sort($arList);
        $arr = array();
        foreach ($arList as $key => $value) {
            if (is_numeric($value)) {
                array_push($arr, array("value" => $value, "name" => hGet($redis, $value, "Object_Name")));
            }
        }
        sort($arList);
        echo json_encode($arr);
        $redis->close();
    } else {
        $arr = Array("isError" => true);
        echo json_encode($arr);
    }
}

if ($par == "getDevsByDevName") {
    $redis = getRedisConect();
    if ($redis) {
        $devname = $_REQUEST['devname'];
        $arList = $redis->keys($devname . "???");
        sort($arList);
        $arr = array();
        foreach ($arList as $key => $value) {
            if (is_numeric($value)) {
                $Object_Name = hGet($redis, $value, "Object_Name");
                $Present_Value = hGet($redis, $value, "Present_Value");
                $Update_Time = hGet($redis, $value, "Update_Time");
                array_push($arr, array("value" => $value, "name" => $Object_Name, 'Present_Value' => $Present_Value, 'update' => $Update_Time));
            }
        }
        sort($arList);
        echo json_encode($arr);
        $redis->close();
    } else {
        $arr = Array("success" => false, 'info' => "link database error!");
        echo json_encode($arr);
    }
}

//Present_Value

if ($par == "gettypes") {
    $redis = getRedisConect();
    if ($redis) {
        $nodeName = $_REQUEST['nodename'];
        $arList = $redis->hKeys($nodeName);
        echo json_encode($arList);
        $redis->close();
    } else {
        $arr = Array("isError" => true);
        echo json_encode($arr);
    }
}

if ($par == "getSchdule") {
    $redis = getRedisConect();
    $arList = $redis->keys("????6??");
    sort($arList);
    $arr = array();
    foreach ($arList as $key => $value) {
        if (is_numeric($value)) {
            array_push($arr, array("value" => $value, "name" => hGet($redis, $value, "Object_Name")));
        }
    }
    sort($arList);
    echo json_encode($arr);
    $redis->close();
    exit(0);
    if ($redis) {
        $arList = array_merge($redis->keys("????601"), $redis->keys("????602"), $redis->keys("????603"), $redis->keys("????604"), $redis->keys("????605"), $redis->keys("????606"), $redis->keys("????607"), $redis->keys("????608"), $redis->keys("????609"), $redis->keys("????610"));

        sort($arList);
        $arr = array();
        foreach ($arList as $key => $value) {
            if (is_numeric($value)) {
                $Object_Name = hGet($redis, $value, "Object_Name");
                array_push($arr, array("value" => $value, "name" => $Object_Name));
            }
        }
        sort($arList);
        echo json_encode($arr);
        $redis->close();
    } else {
        $arr = Array("isError" => true);
        echo json_encode($arr);
    }
}

if ($par == 'tarHome') {
    exec("pwd", $arr);
    echo print_r($arr);

    //exec("tar czvf home.tar.gz keybord",$arr);

    exec("ls", $arr);
    echo json_encode($arr);
}

if ($par == "linkInfo") {
    //echo json_encode($_REQUEST);
    //exit;

    $redis = getRedisConect();
    $arr = array();
    $arr['ip'] = $redis;
    if ($redis) {
        $arr['ip'] = true;
        $nodename = $redis->keys($_REQUEST['nodename'])[0];
        $arr['nodename'] = $nodename;
        if ($nodename) {
            $arr['type'] = hGet($redis, $nodename, $_REQUEST['type']);
            //$arr['type'] = $redis->hGet($nodename, $_REQUEST['type']);
        }
    }
    echo json_encode($arr);

}

if ($par == "login") {
    session_start();
    /*if ($_SESSION['isLogin']) {
        echo json_encode($_SESSION);
        exit();
    }*/
    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];

    $userArr = simplexml_load_file('passwd.xml') or $userArr = false;
    if ($userArr) {
        $userArr = xmlToArray($userArr);
    } else {
        $_SESSION['error'] = 1;
        $_SESSION['errorinfo'] = 'Xml Open Error';
        echo json_encode($_SESSION);
        exit();
    }

    $userArr = $userArr['user'];
    foreach ($userArr as $user) {
        print_r($user);
        echo "<br>";
        if ($user['username'] == $username & $user['password'] == $password) {
            $_SESSION['isLogin'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['password'] = $user['password'];
            $_SESSION['level'] = $user['level'];
        }
    }

    echo json_encode($_SESSION);
}
if ($par == "getSession") {
    session_start();
    echo json_encode($_SESSION);

}
if ($par == "outLogin") {
    session_start();
    $_SESSION['isLogin'] = false;
    $_SESSION['username'] = false;
    $_SESSION['password'] = false;
    $_SESSION['level'] = 0;

}


if ($par == "gettypevalue") {
    $nodeName = $_REQUEST['nodename'];
    $type = $_REQUEST['type'];
    $redis = getRedisConect();

    echo hGet($redis, $nodeName, $type);
    //echo $redis->hGet($nodeName, $type);
    $redis->close();

}

if ($par == "PresentArraySetNull") {
    $redis = getRedisConect();
    $key = $_REQUEST["key"];
    $value = $_REQUEST["value"];
    $number = $_REQUEST["number"];
    $nodeName = $_REQUEST["nodename"];
    $type = $_REQUEST['type'];
    echo json_encode($_REQUEST);
    echo $redis->hSet($nodeName, $type, $value);
    $redis->publish(substr($nodeName, 0, 4) . ".8.*", $nodeName . "\r\nCancel_Priority_Array\r\n" . $number);
    setRedisUpdateTime($redis, $nodename);
    $redis->close();
}

if ($par == "nodes") {
    $redis = getRedisConect();
    $arList = $redis->keys("???????");
    sort($arList);

    getDevs($arList);
    $allArr = array();
    foreach ($arList as $value) {
        $Object_Name = hGet($redis, $value, "Object_Name");

        //$Object_Name = $redis->hGet($value, 'Object_Name');
        //$Object_Name= mb_convert_encoding($Object_Name,'UTF-8','GBK');
        if (strlen($value) == 7 & is_numeric($value)) {
            array_push($allArr, array("leaf" => true, "text" => $Object_Name, 'value' => $value));
        }
    };

    //echo json_encode($allArr);
}
if ($par == 'getDevNames') {

    $redis = getRedisConect();
    $arList = $redis->keys("???????");
    echo json_encode(getDevNames($arList));
}
function getDevNames($arList)
{
    $arr = array();
    foreach ($arList as $value) {
        array_push($arr, substr($value, 0, 4));
    }
    $devs = array_unique($arr);
    return array_values($devs);
}

function getDevs($arList)
{
    $redis = getRedisConect();
    $root = array('text' => $_REQUEST['ip'], 'children' => array());
    $arr = array();

    foreach ($arList as $value) {
        array_push($arr, substr($value, 0, 4));
    }
    $devs = array_unique($arr);
    foreach ($devs as $value) {
        array_push($root['children'], array('leaf' => false, "checked"=>false, 'text' => $value, 'children' => getDevChildren($arList, $value, $redis)));
    }
    echo json_encode($root);
    //return $arr;
}

function getDevChildren($arList, $devValue, $redis)
{
    $types = array('AI', 'AO', 'AV', 'BI', 'BO', 'BV', "SCHDULE");

    $arr = array();
    for ($i = 0; $i <= 6; $i++) {
        $children = getChildren($arList, $devValue . (string)$i, $redis);
        if (sizeof($children)) {
            array_push($arr, array('text' => $types[$i],"checked"=>false, 'leaf' => false, 'children' => $children));
        }
    }
    return $arr;
}

function getChildren($arList, $devValue, $redis)
{
    $arr = array();
    foreach ($arList as $value) {
        if (substr($value, 0, 5) == $devValue) {
            //$Object_Name = $redis->hGet($value, 'Object_Name');
            $Object_Name = hGet($redis, $value, "Object_Name");

            array_push($arr, array('leaf' => true, "checked"=>false, 'text' => $Object_Name, 'value' => $value));
        }
    }
    return $arr;
}


if ($par == "changevalue") {
    $redis = getRedisConect();
    $nodeName = $_REQUEST["nodename"];
    $type = $_REQUEST["type"];
    if (isset($_REQUEST["value"])) {
        $value = $_REQUEST["value"];
    }
    if (isset($_REQUEST["value"])) {
        $value = $_REQUEST["value"];
    }
    echo json_encode($_REQUEST);

    //echo "{type:'".$type."',value:'"."12313"."'}";
    $redis->hSet($nodeName, $type, $value);
    setRedisUpdateTime($redis, $nodeName);

    $redis->publish(substr($nodeName, 0, 4) . ".8." . rand(1000, 9999), $nodeName . "\r\n" . $type . "\r\n" . $value);
    $redis->close();

}
if ($par == "changevaluenopublish") {
    $redis = getRedisConect();
    if ($redis) {

        $nodeName = $_REQUEST["nodename"];
        $type = $_REQUEST["type"];
        if (isset($_REQUEST["value"])) {
            $value = $_REQUEST["value"];
        }
        if (isset($_REQUEST["value"])) {
            $value = $_REQUEST["value"];
        }
        $redis->hSet($nodeName, $type, $value);
        setRedisUpdateTime($redis, $nodeName);

        $redis->close();

    }
}

if ($par == "devPublish") {
    $redis = getRedisConect();
    if ($redis) {
        $key = $_REQUEST["key"];
        $value = $_REQUEST["value"];
        echo $redis->publish($key, $value);
        $redis->close();
    }
}

function xmlToArray($simpleXmlElement)
{
    $simpleXmlElement = (array)$simpleXmlElement;
    foreach ($simpleXmlElement as $k => $v) {
        if ($v instanceof SimpleXMLElement || is_array($v)) {
            $simpleXmlElement[$k] = xmlToArray($v);
        }
    }
    return $simpleXmlElement;
}

function getRedisConect()
{
    $redis = new Redis();
    $ip = $_REQUEST['ip'];
    $port = $_REQUEST['port'];
    $redis->connect($ip, $port, 0.3) or $redis = false;
    return $redis;
}


if ($par == "getImageData") {
    $fn = "../../home/data.json";
    if (file_exists($fn)) {
        echo file_get_contents($fn);
    } else {
        mkdir("../../home");
        file_put_contents($fn, "");
    }
}
/**
 *
 */
if ($par == 'getHomeFileNames') {
    $dir = "../../home";
    $scanned_directory = array_diff(scandir($dir), array('..', '.'));
    $arr = array_values($scanned_directory);
    echo json_encode($arr);
}

if ($par == "putImageData") {
    $fn = "../../home/data.json";
    $content = $_REQUEST['content'];

    if (file_exists($fn)) {
        echo $content;
        echo file_put_contents($fn, $content);
    } else {
        mkdir("../../home");
        file_put_contents($fn, "");
    }
}
if ($par == "saveImageAsHtml") {
    $graph = $_REQUEST["graph"];
    //$htmlStr = "<script>window.location.href='/graph/index.html?graph=" . $graph . "'</script>";
    $str = '<!DOCTYPE html>' .
        '<html lang="en">' .
        '<head>' .
        '<meta charset="UTF-8">' .
        '<title>Title</title>' .
        '</head>' .
        '<style>' .
        '*{' .
        'margin: 0;' .
        'padding: 0;' .
        '}' .
        'html,body,iframe{' .
        'width:100%;' .
        'height:100%;' .
        'overflow: hidden;' .
        '}' .
        'iframe{' .
        'border:none;' .
        '}' .
        '</style>' .
        '<body>' .
        '</body>' .
        '<script>' .
        'var iframe = document.createElement("iframe");' .
        'var body = document.getElementsByTagName("body")[0];' .
        'body.appendChild(iframe);' .
        'if(!location.hostname){' .
        'var ip = window.prompt("please input IP ","' . $ip . '");' .
        'iframe.src="http://' . $ip . '/graph/index.html?graph=' . $graph . '";' .
        '}else{' .
        'iframe.src="../graph/index.html?graph=' . $graph . '"}' .
        '</script>' .
        '</html>';
    file_put_contents("../../home/" . $graph . ".html", $str);
//    '<iframe id="iframe" src="../graph/index.html?graph=' . $graph . '"></iframe>' .

}
if ($par == "deleteImageData") {

    $fn = "../../home/data.json";
    $content = $_REQUEST['content'];
    echo file_put_contents($fn, $content);
    $graph = $_REQUEST["graph"];
    echo unlink("../../home/" . $graph . ".html");
    //file_put_contents("../../home/" . $graph . ".html", $str);
}
if ($par == "deleteGraphFile") {
    $filename = $_REQUEST["graph"];

    echo unlink("../../home/" . $filename . ".html");
    echo unlink("../../home/" . $filename . ".json");

}
if ($par == "getLinkValues") {
    $datas = json_decode($_REQUEST['datas']);
    /* if(!$datas){
         exit();
     }*/
    $datas = object_array($datas);
    foreach ($datas as $key => $value) {
        $value['value'] = getNodeTypeValue($value);
        $datas[$key] = $value;
    }
    echo json_encode($datas);
}
if ($par == "getSubscribeItemsValues") {
    $datas = json_decode($_REQUEST['datas']);
    $datas = object_array($datas);
    $arr = array();
    foreach ($datas as $data) {
        array_push($arr, array("id" => $data['id'], "value" => getNodeTypeValue($data)));
    }
    echo json_encode($arr);
}
if($par=="getNodeTypeValue"){
    echo getNodeTypeValue($_REQUEST);
}

function getNodeTypeValue($arr)
{
    $ip = $arr['ip'];
    $port = $arr['port'];
    $nodeName = $arr['nodename'];
    $type = $arr['type'];
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

    $redis = new Redis();

    //$ip="192.168.2.20";

    $redis->connect($ip, $port, 0.5) or $redis = false;
    if ($redis) {
        //$value = $redis->hGet($nodeName, $type);
        $value = hGet($redis, $nodeName, $type);

        $redis->close();
        return $value;
    } else {
        return false;
    }

}

/*function callback($redis, $channel, $message, $val)
{
    $ip = $_SERVER["SERVER_ADDR"];
    $arr = array();
    $arr['ip'] = $ip;
    $arr['value'] = $val;
    echo "(";
    echo json_encode($arr);
    echo ")";
    exit;
}

if ($par == "subscribe") {

    ini_set('default_socket_timeout', -1);

    $subnode = $_REQUEST['subnode'];

    $redis = new Redis();

    $redis->connect("127.0.0.1", "6379");

    $channel = $subnode;  // channel

    $redis->psubscribe(array($channel), 'callback');
}
*/

function object_array($array)
{
    if (is_object($array)) {
        $array = (array)$array;
    }
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = object_array($value);
        }
    }

    return $array;
}

if ($par == "beforeUploadGraph") {
    listDir();
}

if ($par == "uploadGraphFiles") {
    $dir = "upload/";
    if (!file_exists($dir)) {
        mkdir($dir);
    }

    echo move_uploaded_file($_FILES["file"]["tmp_name"], $dir . $_FILES["file"]["name"]);
}

if ($par == "afterUploadGraph") {
    echo "更新中。。请勿关闭此页面!";
    echo '<script type="text/javascript">window.onload=function(){alert("upldate success.");}</script>';
    exec("cat upload/autoInstallGraph* > upload/install", $arr);
    print_r($arr);
    exec("tar -xzvf upload/install");
    exec("cp -r graph/ ./../../", $arr);
    print_r($arr);
    exec("rm -rf graph/");
    exec("rm -rf upload/");
    //exec("move ");
    //popen("tar -xzvf /mnt/nandflash/web_arm/www/graph/resources/upload/install -C /mnt/nandflash/web_arm/www/",'r');
    //exec("tar -xzvf /mnt/nandflash/web_arm/www/graph/resources/upload/install -C /mnt/nandflash/web_arm/www/",$arr);
    echo "<br>";
    echo "更新成功";
}

if ($par == 'setdate') {
    ini_set('date.timezone', 'Asia/Chongqing');
    $sDate = $_REQUEST["date"];
    $sTime = $_REQUEST["time"];
    $comm1 = "date -s  '" . $sDate . " " . $sTime . "'";
    echo $comm1;
    exec($comm1, $array);
    $comm2 = "hwclock -w";
    exec($comm2, $array1);
}

function getfiles($path, $fileArr)
{
    $tempArr = array();
    //echo '<div style="color:red">'.$path.'</div>';
    foreach (scandir($path) as $afile) {
        if ($afile == '.' || $afile == '..')
            continue;

        if (is_dir($path . '/' . $afile)) {  //目录
            //echo '<div style="color:red">' . $path . '/' . $afile . "</div>";
            $arr = array();
            $arr['text'] = $afile;
            $arr['url'] = $path . '/' . $afile;
            $arr['leaf'] = false;
            $arr['children'] = getfiles($path . '/' . $afile, $tempArr);
            $arr['allowDrop'] = false;
            $arr['allowDrag'] = false;
            $arr['expanded'] = true;
            array_push($tempArr, $arr);
        } else {
            $arr = array();
            $spath = $path . '/' . $afile;
            $arr['text'] = substr($afile, 0, strlen($afile) - 4);
            //$arr['url'] = 'resources/'.$spath;
            $arr['leaf'] = true;
            $arr["icon"] = "resources/" . $spath;
            //$arr['iconCls'] = 'fa-file-image-o';
            $arr['iconCls'] = 'aaaaaa';
            $arr['qtitle'] = substr($afile, 0, strlen($afile) - 4);
            $arr['qtip'] = "<img src=" . "resources/" . $spath . ">";
            //$arr['url'] = 'resources/SvgHvac/' . substr($spath, 4, strlen($spath) - 8) . '.gif';
            $arr['url'] = "resources/" . $spath;
            $arr['src'] = "resources/" . $spath;

            //$binary = file_get_contents($spath);
            //$base64 = base64_encode($binary);
            //$arr['svgurl'] = $binary;//'data:image/gif;base64,'.$base64;

            //$arr['svgurl']='data:image/gif;base64,R0lGODlhHQAUALMOAP///8zMzLKyzMyymcyZsv/M5f/lzOXl/8zM5eXMsuWyzLKZf5mZsrJ/mf///wAAACH5BAEAAA4ALAAAAAAdABQAAATO0MlApZ314qADSAB3eUjYAYo5ASwoOh5bvjGqBiA40GUp0KkUQeTRqWAA39EjXAIGx5UgiiQsAwvAIgMLMAAMLqUBaFQ8hicgzfEcANN3G1AAWOuUtH7dfvsBB211g3QUa2psSG9xJh51dyE4h2kJbSV/CG0phAqGaVCJbnCAjXR2phQge5Uwl6SZMJumnUUftnM9SaVBNnNQUDSjU8FWVkRQWTsWAVNfP8tWZEMrv05Tw8unxhPITzTNcDTRdkRqwNnXTsU3FO007mLwHBEAOw==';
            $arr['allowDrag'] = true;
            $arr['allowDropl'] = false;
            array_push($tempArr, $arr);
        }
    }
    return array_values($tempArr);
} //列出所有文件

function listDir()
{


    $dir = __DIR__;
    $index = strripos($dir, "/");
    $dir = substr(__DIR__, 0, $index);
    $index = strripos($dir, "/");
    $dir = substr(__DIR__, 0, $index);

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

    /*
        //$telnet->write("cd /mnt/nandflash/web_arm/www/program\r\n");

        echo $telnet->read_till(":> ");

        //$telnet->write("chmod 777 * -R\r\n");
        $telnet->write("ls\r\n");
        echo $telnet->read_till(":> ");

        $telnet->write("sh t.bash\r\n");

        echo $telnet->read_till(":> ");

        */
}


function uploadProgram($name)
{
    $telnet = getTelnet();

    $telnet->write("cd ../../\r\n");
    echo $telnet->read_till(":> ");
    $telnet->write("tar czvf $name\r\n");
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
    $telnet = new telnet("192.168.253.253", 23);
    echo $telnet->read_till("login: ");
    $telnet->write("$username\r\n");
    echo $telnet->read_till("password: ");
    $telnet->write("$password\r\n");
    return $telnet;
}

function hGet($redis, $nodename, $type)
{
    $value = $redis->hGet($nodename, $type);

    if (preg_match("/[\x7f-\xff]/", $value)) {  //判断字符串中是否有中文
        return "base64=" . base64_encode($value);
    } else {
        return $value;
    }
    //return mb_convert_encoding($value, "UTF-8", "GBK");
}

function setRedisUpdateTime($redis, $nodename)
{
    $redis->hSet($nodename, "Update_Time", date("Y-m-d h:i:s"));
}

/*
function listDir($dir)
{


        if (is_dir($dir)) {
              if ($dh = opendir($dir)) {
                  while (($file = readdir($dh)) !== false) {
                      if ((is_dir($dir . "/" . $file)) && $file != "." && $file != "..") {
                          $path = $dir . "/" . $file;

                          $telnet->write("chmod 777 $path -R\r\n");
                          echo $path;
                          echo $telnet->read_till(":> ");
                          echo "<br>";
                          //listDir($dir . "/" . $file . "/");

                          //chmod($dir . '/' . $file, 0777);
                      } else {
                          if ($file != "." && $file != "..") {
                              //chmod($dir . '/' . $file, 0777);
                          }
                      }
                  }
                  closedir($dh);
              }
          }


}*/
