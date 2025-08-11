<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Block\Block;
use Concrete\Core\View\View;

/** @var Block $b */

/** @noinspection PhpUnhandledExceptionInspection */
View::element('permission/lists/block', ['b' => $b]);
