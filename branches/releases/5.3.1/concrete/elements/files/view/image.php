<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php 
$path = $fv->getRelativePath();
print '<img src="' . $path . '" />';
