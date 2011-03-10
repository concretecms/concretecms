<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?
$path = $fv->getRelativePath();
print '<img src="' . $path . '" />';
