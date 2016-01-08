<?php defined('C5_EXECUTE') or die("Access Denied."); ?> 
<div style="text-align: left">
<?php
$fh = Loader::helper('file');
print '<pre style="font-size: 11px; font-family: Courier">';
print Loader::helper('text')->entities($fv->getFileContents());
print '</pre>';?>

</div>
