<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<div style="text-align: left">
<?
$fh = Loader::helper('file');
$path = $fv->getPath();
print '<pre style="font-size: 11px; font-family: Courier">';
print Loader::helper('text')->entities($fh->getContents($path));
print '</pre>';?>

</div>
