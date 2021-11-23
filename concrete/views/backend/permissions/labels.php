<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Permission\Access\Access $pa
 * @var Concrete\Core\Permission\Key\Key $pk
 */

View::element('permission/labels', ['pk' => $pk, 'pa' => $pa]);
