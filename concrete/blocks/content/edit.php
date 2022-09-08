<?php

defined('C5_EXECUTE') or die('Access Denied.');
/** @var \Concrete\Block\Content\Controller $controller */
echo '<div class="ccm-block-content-edit-inline">';
echo app('editor')->outputPageInlineEditor('content', $controller->getContentEditMode());
echo '</div>';