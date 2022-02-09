<?php

defined('C5_EXECUTE') or die('Access Denied.');
/** @var \Concrete\Block\Content\Controller $controller */

$content = $controller->getContent();
$forbiddenTags = ['iframe', 'script', 'object', 'embed'];
foreach ($forbiddenTags as $forbiddenTag) {
    $content = preg_replace('~<' . $forbiddenTag . '[^>]*>(.*)</' . $forbiddenTag . '>~i', '$2', $content);
    $content = preg_replace('~<' . $forbiddenTag . '[^>]*>~i', '', $content);
}
echo '' . $content;
