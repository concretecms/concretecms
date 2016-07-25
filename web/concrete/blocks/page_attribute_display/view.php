<?php

defined('C5_EXECUTE') or die('Access Denied.');

echo $controller->getOpenTag();
if ($controller->getTitle()) {
    echo '<span class="ccm-block-page-attribute-display-title">' . $controller->getTitle() . '</span>';
}
echo $controller->getContent();
echo $controller->getCloseTag();
