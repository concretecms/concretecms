<?

namespace Concrete\Core\Foundation;
require DIR_BASE_CORE . '/vendor/symfony/class-loader/Symfony/Component/ClassLoader/ClassLoader.php';
use Environment;
class Loader extends \Symfony\Component\ClassLoader\ClassLoader {


	public static function helper($file, $pkgHandle = false) {
	
		static $instances = array();

		$helper = str_replace('/', '\\', $file);
		if (array_key_exists($helper, $instances)) {
        	$instance = $instances[$helper];
		/*
		} else if (array_key_exists($siteclass, $instances)) {
        	$instance = $instances[$siteclass];
        	*/
		} else {
			$classname = 'Concrete\\Core\\Helper\\' . $helper;
			$instance = new $classname();
			
			if (!property_exists($instance, 'helperAlwaysCreateNewInstance') || $instance->helperAlwaysCreateNewInstance == false) {
	            $instances[$class] = $instance;
	        }
		}
		
		if(method_exists($instance,'reset')) {
			$instance->reset();
		}
		
		return $instance;
	}

	/** 
	 * Loads an element from C5 or the site
	 */
	public function element($_file, $args = null, $_pkgHandle= null) {
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



	/**
	 * @deprecated
	 */
	public static function library($lib, $pkgHandle = null) {
		return false;
	}

	/** 
	 * Loads a job file, either from the site's files or from Concrete's
	 */
	public static function job($job, $pkgHandle = null) {
		return false;
	}

	/** 
	 * Loads a model from either an application, the site, or the core Concrete directory
	 */
	public static function model($mod, $pkgHandle = null) {
		return false;
	}
	
}