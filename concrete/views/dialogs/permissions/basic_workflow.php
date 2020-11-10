<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Workflow\Workflow $workflow
 */

View::element('permission/details/basic_workflow', ['workflow' => $workflow]);
