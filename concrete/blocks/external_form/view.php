<?php defined('C5_EXECUTE') or die('Access Denied.');
/** @var Concrete\Block\ExternalForm\Controller $controller */

$path = $controller->getExternalFormFilenamePath();
if ($path) {
    include $path;
}
