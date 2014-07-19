<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
echo $controller->getOpenTag();
echo "<span>".$controller->getTitle()."</span>";
echo $controller->getContent();
echo $controller->getCloseTag();
?>