<?php
$par = $_GET['par'];
//$xmlstr = file_get_contents('passwd.xml');
//xml_parse($arr,$xmlstr);
//echo $arr;
//echo print_r($userArr);
//$userArr = simplexml_load_file('passwd.xml') or $userArr=false;
/*foreach($userArr as  $user){

    echo json_encode($user);
}*/
//exit();



if ($par == "login") {
    session_start();

    if($_SESSION['isLogin']){
        echo json_encode($_SESSION);
        exit();
    }

    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];

    $userArr = simplexml_load_file('passwd.xml') or $userArr=false;
    if($userArr){
        $userArr=xmlToArray($userArr);
    }else{
        $_SESSION['error']=1;
        $_SESSION['errorinfo']='Xml Open Error';
        echo json_encode($_SESSION);
        exit();
    }

    $userArr=$userArr['user'];
    foreach($userArr as  $user){
        if($user['username']==$username&$user['password']==$password){
            $_SESSION['isLogin']=true;
            $_SESSION['username']=$user['username'];
            $_SESSION['password']=$user['password'];
            $_SESSION['level']=$user['level'];
        }
    }

    echo json_encode($_SESSION);
}
if($par=='isLogin'){

    echo $_SESSION;

}
function isLogin($user){
    $userArr = simplexml_load_file('passwd.xml') or $userArr=false;
    foreach($userArr as  $user){
        echo json_encode($user);
    }
}

function userLogin($user){
    session_start();

    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];
    if($user['username']){

    }
}

function xmlToArray($simpleXmlElement){
    $simpleXmlElement=(array)$simpleXmlElement;
    foreach($simpleXmlElement as $k=>$v){
        if($v instanceof SimpleXMLElement ||is_array($v)){
            $simpleXmlElement[$k]=xmlToArray($v);
        }
    }
    return $simpleXmlElement;
}

?>

