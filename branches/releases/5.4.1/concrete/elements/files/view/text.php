<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
<div style="text-align: left">
<?php 
$path = $fv->getPath();
print '<pre style="font-size: 11px; font-family: Courier">';
print Loader::helper('text')->entities(file_get_contents($path));
print '</pre>';?>

</div>
