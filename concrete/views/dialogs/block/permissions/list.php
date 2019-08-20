<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Legacy\Loader;

Loader::element('permission/lists/block', array('b' => $b, 'rcID' => $rcID));
