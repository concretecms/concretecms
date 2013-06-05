<?

defined('C5_EXECUTE') or die("Access Denied.");


class Concrete5_Library_NewsflowItem {
	
	public function getID() {return $this->id;}
	public function getTitle() {return $this->title;}
	public function getContent() {return $this->content;}
	public function getDate() {return $this->date;}
	public function getDescription() {return $this->description;}
	
	public static function parseResponse($r) {
		try {
			// Parse the returned XML file
			$obj = @Loader::helper('json')->decode($r);
			if (is_object($obj)) {
				$mi = new NewsflowItem();
				$mi->title = $obj->title;
				$mi->content = $obj->content;
				$mi->id = $obj->id;
				$mi->description = $obj->description;
				$mi->date = $obj->date;
				return $mi;
			}
		} catch (Exception $e) {
			throw new Exception(t('Unable to parse news response.'));
		}

	}
	
}