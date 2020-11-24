<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\ImageEditor\EditorServiceProvider;

/**
 * @var Concrete\Core\Entity\File\Version $fv
 * @var Concrete\Core\Application\Application $app
 */

/** @var EditorServiceProvider $editorService */
$editorService = $app->make(EditorServiceProvider::class);
$editorService->renderActiveEditor($fv);