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
	'PageList' => 'Core\Page\List',
	'Block' => 'Core\Page\Block',
	'Conversation' => 'Core\Conversation\Conversation',
	'Package' => 'Core\Package\Package',
	'Collection' => 'Core\Page\Collection',
	'Area' => 'Core\Area\Area',
	'Job' => 'Core\Job\Job',
	'File' => 'Core\File\File',
	'FileSet' => 'Core\FileSet\FileSet',
	'FileList' => 'Core\File\List',
	'QueueableJob' => 'Core\Job\QueueableJob',
	'User' => 'Core\User\User',
	'Log' => 'Core\Log\Log',
	'StartingPointPackage' => 'Core\Package\StartingPointPackage',
	'AuthenticationType' => 'Core\Authentication\AuthenticationType',
	'ConcreteAuthenticationTypeController' => 'Core\Authentication\Type\Concrete',
	'FacebookAuthenticationTypeController' => 'Core\Authentication\Type\Facebook',
	'GroupTree' => 'Core\Foundation\Tree\Type\Group',
	'GroupTreeNode' => 'Core\Foundation\Tree\Node\Type\Group'

));