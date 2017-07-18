<?php
$par = $_REQUEST['par'];
$fn = $_REQUEST['fileName'];
if ($par == 'save') {
    $content = $_REQUEST['content'];
    echo file_put_contents($fn, $content);
}
if ($par == 'get') {
    if (substr($fn, -3) == "xml") {
        header("Content-type:text/xml");
    }
    if(isset($_REQUEST["header"])){
        header($_REQUEST["header"]);
        //header("Content-Disposition: attachment; filename=".basename ($fn) );
    }
    echo file_get_contents($fn);
}
?>