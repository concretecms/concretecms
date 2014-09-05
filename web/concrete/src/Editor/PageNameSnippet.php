<?php
namespace Concrete\Core\Editor;
use Loader;
use Page;
class PageNameSnippet extends Snippet {


	public function replace() {
		$c = Page::getCurrentPage();
		if (is_object($c)) {
			return $c->getCollectionName();
		}
	}



}
