<?php


class user
{
    public $username = null;
    public $password = null;
    public $level = 0;
    public $xml = null;
    public $xmlPath = '../passwd.xml';
    public static $xmlFileName = '../passwd.xml';

    public function __construct($username = null)
    {
        //$this->password=$password;
        $xml = simplexml_load_file($this->xmlPath);
        $this->xml = $xml;
        if ($username) {
            $this->username = $username;

            $queryUser = $this->xml->xpath("/Users/user[username='$username']");
            if ($queryUser) {
                $this->password = $queryUser[0]->password;
                $this->level = $queryUser[0]->level;
            }
        }
    }

    public function __destruct()
    {

    }

    /*public function __set($name, $value)
    {

        // TODO: Implement __set() method.
    }*/

    public function addUser(user $user)
    {

        $queryUser = $this->xml->xpath("/Users/user[username='$user->username']");
        if ($queryUser) {
            return array('success' => false, 'info' => 'User already exists .');
        } else {
            $newDom = $this->xml->addChild("user");
            $newDom->addChild("username", $user->username);
            $newDom->addChild("password", $user->password);
            $newDom->addChild("level", $user->level);
            $this->saveXml();
            return array('success' => true, 'info' => "Increase user success .");
        }
    }

    public static function getUsers()
    {
        $xml = simplexml_load_file(self::$xmlFileName);

        $users = $xml->xpath('/Users/user/username');
        return $users;
    }

    public function deleteUser(user $user)
    {

        $queryUser = $this->xml->xpath("/Users/user[username='$user->username']");
        if ($queryUser) {

            $doc = new DOMDocument();
            $doc->load($this->xmlPath);
            $users = $doc->getElementsByTagName('user');
            foreach ($users as $xmlUser) {
                $username = $xmlUser->getElementsByTagName('username')->item(0);
                $level =    $xmlUser->getElementsByTagName('level')->item(0);
                $nodeValue = (string)$username->nodeValue;
                if ($nodeValue == $user->username & $this->level>$level) {
                    print_r($username->parentNode->parentNode->removeChild($username->parentNode));
                }else{
                    return array('success'=>false,'info'=>'Your permissions lack .');
                }
            }
            $doc->save($this->xmlPath);
            return array('success' => true, 'info' => "delay user successfully .");
        } else {
            return array('success' => false, 'info' => "the user does not exist .");
        }
    }

    public function changeUser(user $user)
    {
        $queryUser = $this->xml->xpath("/Users/user[username='$user->username']");
        if ($queryUser) {
            $queryUser[0]->password = $user->password;
            $queryUser[0]->level = $user->level;
            return array('success' => true, 'info' => "change user successfully .");
        } else {
            return array('success' => false, 'info' => "the user does not exist .");
        }

    }

    public function saveXml()
    {
        $this->xml->saveXML($this->xmlPath);
    }


    public static function login()
    {

        if (isset($_SESSION['isLogin']) & $_SESSION['isLogin']) {
            return $_SESSION;
        }

        if (isset($_REQUEST['username']) and $_REQUEST['password']) {
            $username = $_REQUEST['username'];
            $password = $_REQUEST['password'];

            $xml = simplexml_load_file(self::$xmlFileName);

            $user = $xml->xpath("/Users/user[username='$username' and password='$password']");
            if ($user) {
                $_SESSION['username'] = (String)$user[0]->username;
                $_SESSION['password'] = (String)$user[0]->password;
                $_SESSION['level'] = (String)$user[0]->level;
                $_SESSION['isLogin'] = true;
                $_SESSION['success'] = true;
                return $_SESSION;
            } else {
                $_SESSION = null;
                $_SESSION['isLogin'] = false;
                $_SESSION['info'] = 'username or password is incorret 。';
                return $_SESSION;
            }
        } else {

            return $_SESSION;
        }
    }

    public static function isLogin()
    {
        return $_SESSION;
    }

    public static function outLogin()
    {

        $_SESSION['isLogin'] = false;
        $_SESSION['username'] = null;
        $_SESSION['password'] = null;
        return $_SESSION;
    }
}

//$user = new user();
//$user2 = new User();
//$user2->username = "aaa";
//$user2->password = "bbb";
//echo json_encode($user->addUser($user2));
//$user->getUsers($user2);
//$user->deleteUser($user2);
//$user->changeUser($user2);
//$ar = $user->login("User", "123456");
//echo json_encode($ar);