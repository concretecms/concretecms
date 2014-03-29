<?php

namespace Concrete\Core\Foundation;
use \Symfony\Component\ClassLoader\ClassLoader as SymfonyClassloader;
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
		$this->setupAliasAutoloader();
		$this->setupCoreAutoloader();
		$this->setupLegacyAutoloader();
	}

	/** 
	 * Responsible for autoloading core classes based on their names, not based on PSR-0
	 */
	protected function setupCoreAutoloader() {
		$loader = $this;
		spl_autoload_register(function($class) use ($loader) {
			if ($theme = strstr($class, 'PageTheme', true)) {
				$className = $loader->getClassName('Theme\\' . $class);

				if (substr_count($className, '\\Theme\\') === 1) {
					spl_autoload_call($className);
				}
			}
		});
	}

	protected function setupLegacyAutoloader() {
		$mapping = array(
		    'Loader' => DIR_BASE_CORE . '/classes/Concrete/Core/Legacy/Loader.php',
		);

		$loader = new SymfonyMapClassloader($mapping);
		$loader->register();
	}

	public function getClassName($classPartialPath) {
		$vendor = NAMESPACE_SEGMENT_APPLICATION;
		$env = Environment::get();
		$rec = $env->getRecord($this->classToFile($vendor . '\\' . $classPartialPath));
		if (!$rec->exists()) {
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
		$symfonyLoader->setUseIncludePath(true);
		$symfonyLoader->addPrefix('Concrete', DIR_BASE_CORE . '/' . DIRNAME_CLASSES);
		$symfonyLoader->addPrefix('Symfony', DIR_BASE_CORE . '/vendor/symfony/');
		$symfonyLoader->register();
	}

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

/*	public function element($_file, $args = null, $_pkgHandle= null) {
		if (is_array($args)) {
			$collisions = array_intersect(array('_file', '_pkgHandle'), array_keys($args));
			if ($collisions) {
				throw new Exception(t("Illegal variable name '%s' in element args.", implode(', ', $collisions)));
			}
			$collisions = null;
			extract($args);
		}

		include(Environment::get()->getPath(DIRNAME_ELEMENTS . '/' . $_file . '.php', $_pkgHandle));
	}

	public function mapClassesToPath($array) {

		$cl = new \Symfony\Component\ClassLoader\MapClassLoader($array);
		$cl->register();
	}

	public static function autoload($class) {

		if (strpos($class, 'Controller') > 0) {
			$env = Environment::get();
			$class = substr($class, 0, strpos($class, 'Controller'));
			$handle = Object::uncamelcase($class);
			$path = str_replace('_', '/', $handle);
			$path = $env->getPath(DIRNAME_CONTROLLERS . '/' . $path . '.php');
			require_once($path);
		}


	}

	*/



}