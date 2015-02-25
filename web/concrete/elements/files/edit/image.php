<?php
defined('C5_EXECUTE') or die("Access Denied.");
$view = new View;
$view->setInnerContentFile(DIR_BASE_CORE . '/' . DIRNAME_VIEWS . '/image-editor/editor.php');
echo $view->renderViewContents(array('fv' => $fv));
