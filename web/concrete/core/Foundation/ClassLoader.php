<?php

namespace Concrete\Core\Foundation;
use \Concrete\Core\Foundation\Object;
use \Concrete\Core\Foundation\ModifiedPSR4ClassLoader as SymfonyClassloader;
use \Symfony\Component\ClassLoader\MapClassLoader as SymfonyMapClassloader;

class ClassLoader  {

	static $instance;
	protected $classAliases = array();

	public static function getInstance() {
		if (!isset(static::$instance)) {
			static::$instance = new Classloader();
		}
		return static::$instance;
	}

	public function __construct() {
		$this->setupFileAutoloader();
		$this->setupThirdPartyAutoloader();
		$this->setupAliasAutoloader();
		$this->setupLegacyAutoloader();
	}

	protected function setupThirdPartyAutoloader() {
		\Zend_Loader_Autoloader::getInstance();
		$mapping = array(
			'PasswordHash' => DIR_BASE_CORE . '/vendor/phpass/PasswordHash.php',
			'URLify' => DIR_BASE_CORE . '/vendor/urlify/urlify.php',
			'Mobile_Detect' => DIR_BASE_CORE . '/vendor/mobile_detect/Mobile_Detect.php',
			'Securimage' => DIR_BASE_CORE . '/vendor/securimage/securimage.php',
			'Securimage_Color' => DIR_BASE_CORE . '/vendor/securimage/securimage_color.php'
		);

		$loader = new SymfonyMapClassloader($mapping);
		$loader->register();
	}

	protected function setupLegacyAutoloader() {
		$mapping = array(
		    'Loader' => DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Legacy/Loader.php',
		    'TaskPermission' => DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Legacy/TaskPermission.php',
		    'FilePermissions' => DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Legacy/FilePermissions.php'
		);

		$loader = new SymfonyMapClassloader($mapping);
		$loader->register();
	}

	protected function setupAliasAutoloader() {
		$loader = $this;
		spl_autoload_register(function($class) use ($loader) {
			$list = ClassAliasList::getInstance();
			if (array_key_exists($class, $aliases = $list->getRegisteredAliases())) {
				// we have an alias for it, but we don't have it yet loaded
				// (because after all, we're in the auto loader.)
				$fullClass = $aliases[$class];
				if (!class_exists($fullClass, false)) {
					spl_autoload_call($fullClass);
				}
				// finally, we set up a class alias for this list. We do this now because
				// we don't know earlier what namespace it'll be in
				class_alias($fullClass, $class);
			}
		});
	}

	protected function setupFileAutoloader() {
		$symfonyLoader = new SymfonyClassloader();
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Attribute', DIR_BASE_CORE . '/' . DIRNAME_ATTRIBUTES);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Authentication', DIR_BASE_CORE . '/' . DIRNAME_AUTHENTICATION);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Block', DIR_BASE_CORE . '/' . DIRNAME_BLOCKS);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Theme', DIR_BASE_CORE . '/' . DIRNAME_THEMES);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Controller', DIR_BASE_CORE . '/' . DIRNAME_CONTROLLERS);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Job', DIR_BASE_CORE . '/' . DIRNAME_JOBS);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Core', DIR_BASE_CORE . '/' . DIRNAME_CLASSES);

		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_APPLICATION . '\\Attribute', DIR_APPLICATION. '/' . DIRNAME_ATTRIBUTES);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_APPLICATION . '\\Authentication', DIR_APPLICATION . '/' . DIRNAME_AUTHENTICATION);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_APPLICATION . '\\Block', DIR_APPLICATION . '/' . DIRNAME_BLOCKS);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_APPLICATION . '\\Theme', DIR_APPLICATION . '/' . DIRNAME_THEMES);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_APPLICATION . '\\Controller', DIR_APPLICATION . '/' . DIRNAME_CONTROLLERS);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_APPLICATION . '\\Job', DIR_APPLICATION . '/' . DIRNAME_JOBS);

		$symfonyLoader->register();
	}



}