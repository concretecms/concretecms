<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$info = $controller->getContent();
$bt->inc('form_setup_html.php', array('info' => $info, 'c' => $c, 'b' => $b));
?>