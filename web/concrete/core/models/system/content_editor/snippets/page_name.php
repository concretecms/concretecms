<?

class Concrete5_Model_PageNameSystemContentEditorSnippet extends SystemContentEditorSnippet {


	public function replace() {
		$c = Page::getCurrentPage();
		if (is_object($c)) {
			return $c->getCollectionName();
		}
	}
	
	

}