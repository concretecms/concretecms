<?
namespace Concrete\Core\Logging;
use \Concrete\Core\Foundation\Collection\Database\DatabaseItemList;
class LogList extends DatabaseItemList {

	protected $autoSortColumns = array('time', 'logID');

	function __construct() {
		$this->setQuery("select Logs.logID from Logs");
	}

	public function get($itemsToGet = 100, $offset = 0) {
		$r = parent::get( $itemsToGet, intval($offset));
		$entries = array();
		foreach($r as $row) {
			$e = LogEntry::getByID($row['logID']);
			if (is_object($e)) {
				$entries[] = $e;
			}
		}
		return $entries;
	}

    public function filterByChannel($channel)
    {
        $this->filter('channel', $channel);
    }
	
}