<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\ImageEditor\ImageEditorService;

/**
 * @var Concrete\Core\Entity\File\Version $fv
 * @var Concrete\Core\Application\Application $app
 */

/** @var ImageEditorService $editorService */
$editorService = $app->make(ImageEditorService::class);
$editorService->renderActiveEditor($fv);