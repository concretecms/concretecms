<?php defined('C5_EXECUTE') or die("Access Denied.");

$tag = Core::make('html/image', array($f))->getTag();
$tag->addClass('ccm-image-block img-responsive');
if ($altText) {
    $tag->alt($altText);
}
if ($title) {
    $tag->title($title);
}
if ($linkURL):
    print '<a href="' . $linkURL . '">';
endif;

print $tag;

if ($linkURL):
    print '</a>';
endif;
