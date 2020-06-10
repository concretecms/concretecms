<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\File\Version;
use Concrete\Core\ImageEditor\ImageEditor;

/** @var Version $fv */
/** @var ImageEditor $editor */
$editor = $app->make('editor/image/core');

echo $editor->getView($fv)->render();
