<?
defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
$areaStyle = $c->getAreaCustomStyle($a);

if (is_object($areaStyle)) { ?>
    </div>
<? } ?>