<?php
defined('C5_EXECUTE') or die('Access Denied.');

require __DIR__ . '/../classes/Concrete/Core/Foundation/Loader.php';
require __DIR__ . '/../vendor/zend/Loader/Autoloader.php';
require __DIR__ . '/../vendor/autoload.php';

$loader = new \Concrete\Core\Foundation\Loader();
$loader->setUseIncludePath(true);
$loader->addPrefix('Concrete', __DIR__.'/../classes');
$loader->addPrefix('Symfony', __DIR__.'/../vendor/symfony/');
$loader->register();

$list = \Concrete\Core\Foundation\ClassAliasList::getInstance();
$list->registerMultiple(array(
	'Loader' => 'Concrete\Core\Foundation\Loader',
	'Object' => 'Concrete\Core\Foundation\Object',
	'View' => 'Concrete\Core\View\View',
	'Cache' => 'Concrete\Core\Foundation\Cache\Cache',
	'Environment' => 'Concrete\Core\Foundation\Environment\Environment',
	'EnvironmentRecord' => 'Concrete\Core\Foundation\Environment\EnvironmentRecord',
	'CacheLocal' => 'Concrete\Core\Foundation\Cache\CacheLocal',
	'Router' => 'Concrete\Core\Routing\Router',
	'Route' => 'Concrete\Core\Routing\Route',
	'URL' => 'Concrete\Core\Routing\Url',
	'Events' => 'Concrete\Core\Events\Events',
	'Redirect' => 'Concrete\Core\Routing\Redirect',
	'RedirectResponse' => 'Concrete\Core\Routing\RedirectResponse',
	'Request' => 'Concrete\Core\Http\Request',
	'Response' => 'Concrete\Core\Http\Response',
	'ResponseAssetGroup' => 'Concrete\Core\Http\ResponseAssetGroup',
	'Cookie' => 'Concrete\Core\Cookie\Cookie',
	'AssetList' => 'Concrete\Core\Asset\AssetList',
	'Asset' => 'Concrete\Core\Asset\Asset',
	'AssetGroup' => 'Concrete\Core\Asset\AssetGroup',
	'JavascriptAsset' => 'Concrete\Core\Asset\JavascriptAsset',
	'CoreConversationAssetGroup' => 'Concrete\Core\Asset\Group\CoreConversationAssetGroup',
	'AssetPointer' => 'Concrete\Core\Asset\AssetPointer',
	'ConcretePageTheme' => 'Concrete\Core\Page\Theme\ConcretePageTheme',
	'PageTheme' => 'Concrete\Core\Page\Theme\PageTheme',
	'CssAsset' => 'Concrete\Core\Asset\CssAsset',
	'FileTypeList' => 'Concrete\Core\File\FileTypeList',
	'FileType' => 'Concrete\Core\File\FileType',
	'Localization' => 'Concrete\Core\Localization\Localization',
	'Page' => 'Concrete\Core\Page\Page',
	'Collection' => 'Concrete\Core\Page\Collection',
	'InstallController' => 'Concrete\Controller\InstallController'
));

spl_autoload_register(array('Loader', 'autoload'), true);
