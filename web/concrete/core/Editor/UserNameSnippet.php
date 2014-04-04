<?
namespace Concrete\Core\Editor;
use Loader;
class UserNameSnippet extends Snippet {


	public function replace() {
		$u = new User();
		return $u->getUserName();
	}
	

}