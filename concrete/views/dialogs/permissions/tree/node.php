<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Tree\Node\Node $node
 */

View::element('permission/details/tree/node', ['node' => $node]);
