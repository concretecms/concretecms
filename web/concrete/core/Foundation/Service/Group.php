<?
namespace Concrete\Core\Foundation\Service;
use Closure;

/** 
 *  Extending this class allows groups of services to be registered at once.
 */
abstract class Group {	

	public function __construct(Locator $locator) {
		$this->locator = $locator;
	}

	/** 
	 * Registers the services provided by this group.
	 * @return void
	 */
	abstract public function register();

}