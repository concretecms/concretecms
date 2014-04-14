<?
namespace Concrete\Core\Foundation\Service;
use Closure;

class Locator {	

	private $registry = array();
	private $instances = array();

	/** 
	 * Registers a service with the Service Locator. The callback is a closure pointing to the class that should be instantiated
	 *
	 * <code>
	 * use \Concrete\Core\Foundation\ServiceLocator;
	 * $sl = new ServiceLocator();
	 * $sl->register('validation/file', function() {
	 * 	return new \Concrete\Core\Validation\Helper\File;
	 * })
	 * </code>
	 * or
	 * <code>
	 * $entries = array(
	 * 	'pointer1' => '\My\Class',
	 * 	'pointer2' => '\My\Other\Class'
	 * );
	 * $sl->register($entries);
	 * </code>
	 * @param  string $pointer
	 * @param  Closure $callback
	 * @return void
	 * 
	 */
	public function register($pointer, $callback = false) {
		$entries = array();
		if (is_array($pointer)) {
			$entries = $pointer;
		} else {
			$entries[$pointer] = $callback;
		}

		foreach($entries as $pointer => $callback) {
			$this->registry[$pointer] = array($callback, 'new');
		}
	}


	/** 
	 * Registers service with the Service Locator. This service will only be evaluated the first time it is requested.
	 * @param  string $pointer
	 * @param  Closure $callback
	 * @return void
	 *
	 * <code>
	 * $sl = new ServiceLocator();
	 * $sl->singleton('single', function() {
	 * 	new SingleObject();
	 * })
	 */
	public function singleton($pointer, $callback) {
		$this->registry[$pointer] = array($callback, 'shared');
	}

	/** 
	 * Registers a single object with the Service Locator. This is an instance of an object.
	 * @param  string $pointer
	 * @param  mixed $object
	 * @return void
	 *
	 * <code>
	 * $sl->instance('session', $session);
	 * 	 */
	public function instance($pointer, $object) {
		$this->instances[$pointer] = $object;
	}

	/**
	 * Retrieves something from the ServiceLocator and instantiates it.
	 * @param  string $identifier
	 * @return mixed
	 */
	public function make($identifier) {

		if (isset($this->instances[$identifier])) {
			$object = $this->instances[$identifier];
		}

		if (!isset($object)) {
			list($callback, $type) = $this->registry[$identifier];
			if (isset($callback)) {
				if ($callback instanceof Closure) {
					$object = $callback();
				} else {
					$make = $callback;
				}
			} else {
				$make = $identifier;
			}

			if (isset($make)) {
				$object = new $make();
			}

			if ($type == 'shared') {
				$this->instances[$identifier] = $object;
			}

		}

		return isset($object) ? $object : false;
	}

	/** 
	 * Returns true if a service has been registered to this service locator.
	 * @param string $identifier
	 * @return boolean
	 * 
	 */
	
	public function isRegistered($identifier) {
		return isset($this->registry[$identifier]);
	}

	/** 
	 * Loads and registers a class ServiceGroup class.
	 * @param  string $class
	 * @return void
	 */
	public function registerGroup($class) {
		$cl = new $class($this);
		$cl->register();
	}

	/**
	 * Registers an array of service group classes.
	 * @param  array $groups
	 * @return void
	 */
	public function registerGroups($groups) {
		foreach($groups as $group) {
			$this->registerGroup($group);
		}
	}
}