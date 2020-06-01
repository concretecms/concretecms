<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Filesystem\FileLocator;

require app(FileLocator::class)->getRecord(DIRNAME_TOOLS . '/permissions/categories/admin.php')->getFile();
exit;
