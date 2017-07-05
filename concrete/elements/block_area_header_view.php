<?php defined('C5_EXECUTE') or die('Access Denied.');

$c = Page::getCurrentPage();
if ($c) {
    $css = $c->getAreaCustomStyle($a);
}

if (isset($css)) {
    $class = $css->getContainerClass();
    $id = $css->getCustomStyleID();
    $elementAttribute = $css->getCustomStyleElementAttribute();
} else {
    $class = '';
    $id = '';
    $elementAttribute = '';
}

if ($class || $id || $elementAttribute) { ?>
<div
<?php if ($class) { ?>
class="<?php echo $class; ?>"
<?php } ?>
<?php if ($id) { ?>
id="<?php echo $id; ?>"
<?php } ?>
<?php if ($elementAttribute) { ?>
<?php echo $elementAttribute; ?>
<?php } ?>
>
<?php }
