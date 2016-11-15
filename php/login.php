<?php

include_once('user.php');
$par = $_REQUEST['par'];
session_start();


if (isset($_SESSION['username'])) {
    $user = new user($_SESSION['username']);
}
if ($par == 'login') {

    echo json_encode(user::login());

}
if ($par == 'outLogin') {
    echo json_encode(user::outLogin());
}
if ($par == 'addUser') {
    $newUser = new user($_REQUEST['username']);
    $newUser->username = $_REQUEST['username'];
    $newUser->password = $_REQUEST['password'];
    $newUser->level = $_REQUEST['level'];
    if ($_SESSION['isLogin']) {
        $user = new user($_SESSION['username']);
        echo json_encode($user->addUser($newUser));
    } else {
        echo json_encode(array('success' => false, 'info' => "Not Landed ."));
    }
}
if ($par == 'changeUser') {
    $newUser = new user();
    $newUser->username = $_REQUEST['username'];
    $newUser->password = $_REQUEST['password'];
    $newUser->level = $_REQUEST['level'];
    $user->changeUser($newUser);
}
if ($par == 'deleteUser') {
    echo json_encode($user->deleteUser(new user($_REQUEST['username'])));
}
if ($par == 'getAllUser') {
    echo json_encode(user::getUsers());
}
if ($par == 'getLoginInfo') {
    echo json_encode($_SESSION);
}
?>
