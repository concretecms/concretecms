<?php

defined('C5_EXECUTE') or die('Access Denied.');

use HtmlObject\Image;
use Concrete\Core\Entity\File\Version;

/** @var Version $fv */

$tag = new Image();

$tag->setAttribute("src", $fv->getURL());
$tag->setAttribute("alt", h($fv->getTitle()));

if ($fv->getTypeObject()->isSVG()) {
    $tag->addClass('ccm-svg');
    $tag->setAttribute("style", 'max-width: 100%');
}

echo (string) $tag;
