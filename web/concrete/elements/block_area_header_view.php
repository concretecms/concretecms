<?
defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
$css = $c->getAreaCustomStyle($a);

//global $layoutSpacingActive;
//if($layoutSpacingActive) echo 'TESTING'; 

if (is_object($css)) { ?>
<div class="<?=$css->getContainerClass() ?>" >
<? } ?>