<?php

defined('C5_EXECUTE') or die("Access Denied.");
/** @var \Concrete\Block\Content\Controller $controller */
echo app("editor")->outputPageInlineEditor('content', $controller->getContentEditMode());
