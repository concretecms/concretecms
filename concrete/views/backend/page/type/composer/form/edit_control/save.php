<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Page\Type\Composer\FormLayoutSetControl $setControl
 */

View::element('page_types/composer/form/layout_set/control', ['control' => $setControl]);
