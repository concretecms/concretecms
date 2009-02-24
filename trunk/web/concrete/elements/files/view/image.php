<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?
$path = $fv->getRelativePath();
print '<img src="' . $path . '" />';
