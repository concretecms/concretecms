<?php defined('C5_EXECUTE') or die("Access Denied.");

$tag = Core::make('html/image', array($f))->getTag();
$tag->addClass('ccm-image-block img-responsive');
$tag->alt($altText);

if ($linkURL):
    print '<a href="' . $linkURL . '">';
endif;

print $tag;

if ($linkURL):
    print '</a>';
endif;
