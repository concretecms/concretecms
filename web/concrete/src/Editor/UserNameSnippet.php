<?php
namespace Concrete\Core\Editor;
use Loader;
use User;
class UserNameSnippet extends Snippet {


	public function replace() {
		$u = new User();
		return $u->getUserName();
	}


}
