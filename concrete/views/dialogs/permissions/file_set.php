<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\File\Set\Set $fileSet
 */

View::element('permission/details/file_set', ['fileset' => $fileSet]);
