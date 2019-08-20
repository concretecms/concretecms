<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Legacy\Loader;

Loader::element('permission/keys/add_block', array('permissionKey' => $permissionKey, 'permissionAccess' => $permissionAccess));
