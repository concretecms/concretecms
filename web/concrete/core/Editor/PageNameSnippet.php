<?
namespace Concrete\Core\Editor;
use Loader;
class PageNameSnippet extends Snippet {


	public function replace() {
		$c = Page::getCurrentPage();
		if (is_object($c)) {
			return $c->getCollectionName();
		}
	}
	
	

}