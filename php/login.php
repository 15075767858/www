<?php
/**
 * Created by PhpStorm.
 * User: liuzhencai
 * Date: 16/8/11
 * Time: 下午4:10
 */
$par=$_GET['login'];
if($par=="userLogin"){

    if(isset($_SESSION["userLogin"])){
        echo 1;
        //$_SESSION['userLogin']=1;
    }else{
        $password = $_POST["password"];
        if($password=="Admin123"){
            $_SESSION["userLogin"]=1;
            echo 1;
        }else{
            echo 0;
        }
    }
}

if($par=="engineerLogin"){

    if(isset($_SESSION["engineerLogin"])){
        echo 1;
        //$_SESSION['userLogin']=1;
    }else{
        $password = $_POST["password"];
        if($password=="SmartIO"){
            $_SESSION["engineerLogin"]=1;
            echo 1;
        }else{
            echo 0;
        }
    }
}
?>
