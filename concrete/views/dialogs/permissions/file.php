<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Entity\File\File $file
 */

View::element('permission/details/file', ['f' => $file]);
