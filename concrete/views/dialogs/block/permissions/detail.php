<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Legacy\Loader;

Loader::element('permission/details/block', array('b' => $b, 'rcID' => $rcID));
