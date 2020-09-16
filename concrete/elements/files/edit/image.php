<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Entity\File\Version $fv
 * @var Concrete\Core\Application\Application $app
 */

$editor = $app->make('editor/image/core');

echo $editor->getView($fv)->render();
