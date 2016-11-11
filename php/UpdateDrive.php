<?php

$pids = array("bip-client" => "bip_client.pid",
    "bac-logic" => "logic.pid",
    "bac-mrouter" => "router.pid",
    "bac-global" => "bac_global.pid",
    "bac-logic-modbus" => "logic-modbus.pid",
    "bac-server-modbus" => "bac-server-modbus.pid",
    "bac-client" => "bac_client.pid",
    "bac-server" => "bac_server.pid");

$pidFile = "/var/run/" . $pids[$_REQUEST['driveName']];
if (file_exists($pidFile)) {
    $myfile = fopen($pidFile, "r");
    $jc = fgets($myfile);
    $test = "kill " . $jc;
    exec($test, $array);
}
$target='/mnt/nandflash/' . $_REQUEST['driveName'];
$move = move_uploaded_file($_FILES['driveFile']['tmp_name'],$target);
echo json_encode(array('move' => $move));