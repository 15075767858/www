<?php

class EventAlarm
{
    const AlarmEventFile = "../AlarmEvent.json";
    const alarmconfXml = "../graph/alarmconf.xml";
    const alarmconfJson = "../graph/alarmconf.json";
    const alarmhisXml = "../graph/alarmhis.xml";

    const LookType = "Alarm";
    const ListenType = "Present_Value";

    public function __construct()
    {

    }

    public function getAlarmconfXml()
    {
        if (file_exists($this::alarmconfXml)) {
            return file_get_contents($this::alarmconfXml);
        } else {
            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->formatOutput = true;
            $root = $dom->createElement('root');//新建节点
            $dom->appendChild($root);//设置root为跟节点
            $dom->save($this::alarmconfXml, LIBXML_NOEMPTYTAG);
            return file_get_contents($this::alarmconfXml);
        }
    }


    public function setSaveTime($time = 0)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->load($this::alarmhisXml);
        $savetime = $dom->getElementsByTagName("savetime");
        $savetime[0]->nodeValue = $time;

        return $dom->save($this::alarmhisXml);
    }

    public function addLog($arr)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->load($this::alarmhisXml);
        $logs = $dom->getElementsByTagName('logs');
        $types = array("ip", "port", "key", "objectname", "presentvalue", "alarmtxt", "normaltxt", "time");
        $log = $dom->createElement("log");
        foreach ($types as $value) {

            $nodeValue = isset($arr->$value) ? $arr->$value : "";
            $tag = $this->createXmlNode($dom, $value, $nodeValue);
            $log->appendChild($tag);
        }
        $logs[0]->appendChild($log);
        return $dom->save($this::alarmhisXml, LIBXML_NOEMPTYTAG);
    }

    public function getAlarmhisXml()
    {
        if (!file_exists($this::alarmhisXml)) {
            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->formatOutput = true;
            $root = $dom->createElement('root');//新建节点
            $dom->appendChild($root);//设置root为跟节点
            $savetime = $dom->createElement('savetime');//新建节点
            $savetime->nodeValue = 0;
            $root->appendChild($savetime);
            $logs = $dom->createElement('logs');//新建节点
            $root->appendChild($logs);
            $dom->save($this::alarmhisXml);
            return file_get_contents($this::alarmhisXml);
        }
    }

    public function removeTimeoutTag($curtime)
    {
        $this->getAlarmhisXml();

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->load($this::alarmhisXml);
        $timeout = $dom->getElementsByTagName("savetime")[0]->nodeValue or 0;
        $logs = $dom->getElementsByTagName("log");
        for ($i = $logs->length; $i > 0; $i--) {
            $log = $logs[$i - 1];
            $time = $log->getElementsByTagName("time")[0]->nodeValue;
            if ($time + $timeout < $curtime) {
                $log->parentNode->removeChild($log);
            }
        }

        return $dom->save($this::alarmhisXml);
    }

    public function createXmlNode($dom, $nodeName, $nodeValue)
    {
        $tag = $dom->createElement($nodeName);
        $tag->nodeValue = $nodeValue;
        return $tag;
    }

    public function saveAlarmconfXml($content)
    {
        if (!file_exists($this::alarmconfXml)) {
            file_put_contents($this::alarmconfXml, $content);
        }
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->loadXML($content);
        $dom->save($this::alarmconfXml);
        return strlen($content);
        //return file_put_contents($this::alarmconfXml, $content);
    }

    public function saveAlarmhisXml($content)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->loadXML($content);
        $dom->save($this::alarmhisXml);
        return strlen($content);
    }

    public function alarmhisLoadJson()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->load($this::alarmconfXml);
        $items = $dom->getElementsByTagName("item");
        $saveArray = array();
        foreach ($items as $item) {
            $ip = $item->getElementsByTagName("ip")[0]->nodeValue;
            $port = $item->getElementsByTagName("port")[0]->nodeValue;
            $key = $item->getElementsByTagName("key")[0]->nodeValue;
            //if ($this->isListen($ip, $port, $key)) {
            $value = $this->getTypeValue($ip, $port, $key, $this::ListenType);
            $objectname = $item->getElementsByTagName("objectname")[0]->nodeValue;;
            array_push($saveArray, array("ip" => $ip, "port" => $port, "key" => $key, "presentvalue" => $value, "objectname" => $objectname));
            //}
        }

        return file_put_contents($this::alarmconfJson, json_encode($saveArray));
    }

    public function isListen($ip, $port, $key)
    {
        $setAlarm = $this->getTypeValue($ip, $port, $key, $this::LookType);
        $setAlarmJson = json_decode($setAlarm);

        if ($setAlarmJson) {
            if ($setAlarmJson->Set_Alarm[0]->event_type == 0) {
                return true;
            } else {
                return false;
            }
        } else {

            return false;
        }
    }

    public function getTypeValue($ip, $port, $key, $type)
    {
        $redis = $this->getRedisConnect($ip, $port);
        if ($redis) {
            return $redis->hGet($key, $type);
        } else {
            return false;
        }
    }

    public function saveAlarmEvent()
    {
        $content = file_get_contents($this::ipsFile);
        $ips = json_decode($content);
        $ipsArray = array();
        foreach ($ips as $key => $ip) {
            $ipsArray[$ip] = $this->getAllAlarmEvent($ip, 6379);
        }
        return file_put_contents($this::AlarmEventFile, json_encode($ipsArray));
    }

    public function getAllAlarmEvent($ip, $port)
    {
        $AlarmType = "Limit_Enable";
        $EventType = "Event_Enable";

        $redis = $this->getRedisConnect($ip, $port);
        if (!$redis) {
            return false;
        }
        $keys = $redis->keys("???????");
        sort($keys);
        $keyArray = array();
        foreach ($keys as $num => $key) {
            $typeArray = array();
            $AlarmValue = $redis->hGet($key, $AlarmType);
            if (!is_bool($AlarmValue)) {
                $typeArray[$AlarmType] = $AlarmValue;
            }
            $EventValue = $redis->hGet($key, $EventType);
            if (!is_bool($EventValue)) {
                $typeArray[$EventType] = $EventValue;
            }
            if (sizeof($typeArray)) {
                $keyArray[$key] = $typeArray;
            }
        }
        return $keyArray;
    }

    public function getAlarmEventPoint()
    {
        //$this->getRedisConnect()
    }

    public function diffAlarmEvent()
    {
        $beforeData = $this->getAlarmEvent();
        $this->saveAlarmEvent();
        $afterData = $this->getAlarmEvent();
        $resArray = array_diff_assoc($beforeData, $afterData);
        print_r($resArray);
    }


    public function getAlarmEventArray()
    {
        $content = file_get_contents($this->getAlarmEventString());
        return json_decode($content);
    }

    public function getAlarmEventString()
    {
        return file_get_contents($this::AlarmEventFile);
    }

    public function saveAllAlarm()
    {
        echo file_get_contents($this::alarmconfXml);
    }


    public function getRedisConnect($ip, $port)
    {
        error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
        $redis = null;
        try {
            $redis = new Redis();
            if (!$redis) {
                return false;
                //exit(json_encode(array('success' => false, 'info' => $ip . ' Redis error')));
            }
            $redis->connect($ip, $port, 0.3) or $redis = false;
            if (!$redis) {
                return false;
                //exit(json_encode(array('success' => false, 'info' => $ip . 'Connction error')));
            }
        } catch (Exception $e) {
            return false;
            //exit(json_encode(array('success' => false, 'info' => $ip . 'Connction error' . $e)));
        }
        return $redis;
    }


}

$par = $_REQUEST['par'];
$ip = $_SERVER["SERVER_ADDR"];
$eventAlarm = new EventAlarm();
if ($par == "saveAlarmconfXml") {
    echo $eventAlarm->saveAlarmconfXml($_REQUEST['content']);
}
if ($par == "saveAlarmhisXml") {
    echo $eventAlarm->saveAlarmhisXml($_REQUEST['content']);
}
if ($par == "saveAlarmhisJson") {
    echo $eventAlarm->alarmhisLoadJson();
}
if ($par == "setIps") {
    $ips = $_REQUEST['ips'];
    echo $eventAlarm->saveIps($ips);
}

if ($par == "getAlarmEventPoint") {
    echo $eventAlarm->getAlarmEventPoint();
}

if ($par == "getAlarmEventString") {
    echo $eventAlarm->getAlarmEventString();
}


if ($par == "diffAlarmEvent") {
    echo $eventAlarm->diffAlarmEvent();
}
if ($par == "gettypevalue") {

    echo $eventAlarm->gettypevalue($_REQUEST['ip'], $_REQUEST['port'], $_REQUEST['nodename'], $_REQUEST['type']);
}

if ($par == "saveAlarmEvent") {
    echo $eventAlarm->saveAlarmEvent();
}
if ($par == "addLog") {

    $array = file_get_contents("php://input");
    $array = json_decode($array);
    $size = $eventAlarm->addLog($array);
    echo "{success:true,info:$size}";
}
if ($par == "setSaveTime") {
    if ($_REQUEST['alarmhis'] == "true") {
        $time = $_REQUEST['savetime'];
        $size = $eventAlarm->setSaveTime($time);
        echo "{success:true,info:$size}";
    } else {
        $time = 0;
        $size = $eventAlarm->setSaveTime($time);
        echo "{success:true,info:$size}";
    }
}
if ($par == "removeTimeoutTag") {
    $curtime = $_REQUEST['curtime'];
    $size = $eventAlarm->removeTimeoutTag($curtime);
    echo "{success:true,info:$size}";
}

if ($par == "getAlarmconfXml") {
    header('Content-type: text/xml');
    echo $eventAlarm->getAlarmconfXml();
}

if ($par == "getAlarmhisXml") {
    header('Content-type: text/xml');
    echo $eventAlarm->getAlarmhisXml();
}

