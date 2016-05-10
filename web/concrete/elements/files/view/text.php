<?php defined('C5_EXECUTE') or die("Access Denied."); ?> 
<div style="text-align: left">
<?php
$fh = Loader::helper('file');
echo '<pre style="font-size: 11px; font-family: Courier">';
echo Loader::helper('text')->entities($fv->getFileContents());
echo '</pre>';?>

</div>
