<?php defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Page\Page;

$c = Page::getCurrentPage();
if ($c) {
    $css = $c->getAreaCustomStyle($a);
}

if (isset($css)) {
    $class = $css->getContainerClass();
} else {
    $class = '';
}
if ($class !== '') {
    ?></div><?php

}
