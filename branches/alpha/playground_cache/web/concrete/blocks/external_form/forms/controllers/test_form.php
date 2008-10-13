<?
defined('C5_EXECUTE') or die(_("Access Denied."));
class TestFormExternalFormBlockController extends BlockController {

	public function action_test_search() {
		
		$this->set('response', 'Thanks!');
		return true;
	}
	
}