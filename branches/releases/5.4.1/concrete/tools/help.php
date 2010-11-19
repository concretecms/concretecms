<?php 
defined('C5_EXECUTE') or die("Access Denied.");
print Loader::helper("file")->getContents(MENU_HELP_URL);
exit;
