<?
defined('C5_EXECUTE') or die("Access Denied.");
$list = \Concrete\Core\Foundation\ClassAliasList::getInstance();
$list->registerMultiple(array(
	'Cache' => 'Core\Foundation\Cache\Cache',
	'Database' => 'Core\Database\Database',
	'Localization' => 'Core\Localization\Localization',
	'Redirect' => 'Core\Routing\Redirect',
	'RedirectResponse' => 'Core\Routing\RedirectResponse',
	'Request' => 'Core\Http\Request',
	'Response' => 'Core\Http\Response',
	'Cookie' => 'Core\Cookie\Cookie',
	'Page' => 'Core\Page\Page',
	'Collection' => 'Core\Page\Collection',
	'StartingPointPackage' => 'Core\Package\StartingPointPackage'

));