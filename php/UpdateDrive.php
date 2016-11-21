<?php

$par = $_REQUEST['par'];

if ($par == 'beforeUpload') {
    $path = "/mnt/nandflash/".$_REQUEST['fileName'];
    //unset($path);
    $str= 'rm -rf '.$path;
    echo $str;
    echo exec($str);
    $pids = array("bip-client" => "bip_client.pid",
        "bac-logic" => "logic.pid",
        "bac-mrouter" => "router.pid",
        "bac-global" => "bac_global.pid",
        "bac-logic-modbus" => "logic-modbus.pid",
        "bac-server-modbus" => "bac-server-modbus.pid",
        "bac-client" => "bac_client.pid",
        "bac-server" => "bac_server.pid");

    $pidFile = "/var/run/" . $pids[$_REQUEST['fileName']];

    if (file_exists($pidFile)) {
        $myfile = fopen($pidFile, "r");
        $jc = fgets($myfile);
        $test = "kill " . $jc;
        exec($test, $array);
    }


}

if($par=='upload'){
    echo file_put_contents('/mnt/nandflash/'.$_REQUEST['fileName'], file_get_contents($_FILES['file']['tmp_name']), FILE_APPEND);
}
//$target = '/mnt/nandflash/' . $_REQUEST['driveName'];
//$move = move_uploaded_file($_FILES['driveFile']['tmp_name'], $target);
