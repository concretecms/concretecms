<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Page\Type\Type $pageType
 */

View::element('permission/details/page_type', ['pagetype' => $pageType]);
