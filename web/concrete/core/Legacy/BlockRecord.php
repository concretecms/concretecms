<?
namespace Concrete\Core\Legacy;
use Loader;
/**
 * @deprecated
 */
class BlockRecord extends Model {

	public $bID;

	public function __construct($_table) {
		parent::__construct($_table);
	}

}