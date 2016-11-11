<?php
$par = $_REQUEST['par'];
$fn = $_REQUEST['fileName'];

if ($par == 'save') {
    $content = $_REQUEST['content'];
    echo file_put_contents($fn, $content);
}
if ($par == 'get') {
    echo file_get_contents($fn);
}
?>