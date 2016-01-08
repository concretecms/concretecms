<?php
defined('C5_EXECUTE') or die("Access Denied.");

print Core::make("editor")->outputPageInlineEditor('content', $controller->getContentEditMode());
