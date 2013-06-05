<?

class Concrete5_Model_UserNameSystemContentEditorSnippet extends SystemContentEditorSnippet {


	public function replace() {
		$u = new User();
		return $u->getUserName();
	}
	

}