<?php

namespace Concrete\Core\Foundation;
use \Concrete\Core\Foundation\Object;
use \Symfony\Component\ClassLoader\PSR4ClassLoader as SymfonyClassloader;
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
		);

		$loader = new SymfonyMapClassloader($mapping);
		$loader->register();
	}

	/*

	protected function setupCoreCustomAutoloaders() {
		$loader = $this;
		spl_autoload_register(function($class) use ($loader) {
			$segments = explode('\\', $class);
			if ($segments[0] == '') {
				$segments = array_slice($segments, 1);
			}
			$path = DIR_BASE_CORE;
			if ($segments[0] == NAMESPACE_SEGMENT_APPLICATION) {
				$path = DIR_BASE;
			}
			if (isset($segments[3]) && $segments[3] == 'Controller' && $segments[1] == 'Attribute') {
				$path .= '/' . DIRNAME_ATTRIBUTES . '/' . Object::uncamelcase($segments[2]) . '/' . FILENAME_CONTROLLER;
				require($path);
			}

			if (isset($segments[3]) && $segments[3] == 'Controller' && $segments[1] == 'Block') {
				$path .= '/' . DIRNAME_BLOCKS . '/' . Object::uncamelcase($segments[2]) . '/' . FILENAME_CONTROLLER;
				require($path);
			}


		});
	}
	*/	
	protected function setupLegacyAutoloader() {
		$mapping = array(
		    'Loader' => DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Legacy/Loader.php'
		);

		$loader = new SymfonyMapClassloader($mapping);
		$loader->register();
	}

	public static function getClassName($classPartialPath) {
		$vendor = NAMESPACE_SEGMENT_APPLICATION;
		if (!class_exists('\\' . $vendor . '\\' . $classPartialPath)) {
			$vendor = NAMESPACE_SEGMENT_VENDOR;
		}
		$fullClass = '\\' . $vendor . '\\' . $classPartialPath;
		return $fullClass;
	}
	protected function setupAliasAutoloader() {
		$loader = $this;
		spl_autoload_register(function($class) use ($loader) {
			$list = ClassAliasList::getInstance();
			if (array_key_exists($class, $aliases = $list->getRegisteredAliases())) {
				// we have an alias for it, but we don't have it yet loaded
				// (because after all, we're in the auto loader.)
				$fullClass = $loader->getClassName($aliases[$class]);
				spl_autoload_call($fullClass);

				// finally, we set up a class alias for this list. We do this now because
				// we don't know earlier what namespace it'll be in
				class_alias($fullClass, $class);
			}
		});
	}

	protected function setupFileAutoloader() {
		$symfonyLoader = new SymfonyClassloader();
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Attribute', DIR_BASE_CORE . '/' . DIRNAME_ATTRIBUTES);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Block', DIR_BASE_CORE . '/' . DIRNAME_BLOCKS);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Theme', DIR_BASE_CORE . '/' . DIRNAME_THEMES);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Helper', DIR_BASE_CORE . '/' . DIRNAME_HELPERS);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Controller', DIR_BASE_CORE . '/' . DIRNAME_CONTROLLERS);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Job', DIR_BASE_CORE . '/' . DIRNAME_JOBS);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Core', DIR_BASE_CORE . '/' . DIRNAME_CLASSES);

		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_APPLICATION . '\\Attribute', DIR_BASE. '/' . DIRNAME_ATTRIBUTES);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_APPLICATION . '\\Block', DIR_BASE . '/' . DIRNAME_BLOCKS);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_APPLICATION . '\\Theme', DIR_BASE . '/' . DIRNAME_THEMES);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_APPLICATION . '\\Helper', DIR_BASE . '/' . DIRNAME_HELPERS);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_APPLICATION . '\\Controller', DIR_BASE . '/' . DIRNAME_CONTROLLERS);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_APPLICATION . '\\Job', DIR_BASE . '/' . DIRNAME_JOBS);
		$symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Core', DIR_BASE . '/' . DIRNAME_CLASSES);

		$symfonyLoader->register();
	}
/*
	public function classToFile($class) {
		if ('\\' == $class[0]) {
		    $class = substr($class, 1);
		}

		if (false !== $pos = strrpos($class, '\\')) {
		    // namespaced class name
		    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 0, $pos)) . DIRECTORY_SEPARATOR;
		    $className = substr($class, $pos + 1);
		} else {
		    // PEAR-like class name
		    $classPath = null;
		    $className = $class;
		}

		$classPath .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		return $classPath;
	}
	*/



}