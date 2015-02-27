<?php
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getCurrentPage();
$css = $c->getAreaCustomStyle($a);
if (is_object($css)) {
    $class = $css->getContainerClass();
}

if ($class) { ?>
</div>
<?php } ?>