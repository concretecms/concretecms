<?php
defined('C5_EXECUTE') or die("Access Denied.");

$controller->nextLabel=t('Next');
$controller->previousLabel=t('Previous');
$controller->parentLabel=t('Up');
$controller->showArrows=1;
$controller->loopSequence=1;
$controller->orderBy='display_asc';

$this->inc('form_setup_html.php', array('controller'=>$controller));
