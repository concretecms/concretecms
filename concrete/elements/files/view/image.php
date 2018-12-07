<?php
use HtmlObject\Image;

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Entity\File\Version $fv */

$tag = new Image();
$tag->src = $fv->getURL();
$tag->alt = h($fv->getTitle());
if ($fv->getTypeObject()->isSVG()) {
    $tag->addClass('ccm-svg');
    $tag->style = 'max-width: 100%';
}
echo (string) $tag;
