<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$v = View::getInstance();
$v->setTheme('concrete');
$v->render('/upgrade');
exit;
