<?
use Concrete\Core\Editor;
class UserNameSnippet extends Snippet {


	public function replace() {
		$u = new User();
		return $u->getUserName();
	}
	

}