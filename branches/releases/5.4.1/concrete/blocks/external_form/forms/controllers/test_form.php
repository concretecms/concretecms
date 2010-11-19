<?php 
defined('C5_EXECUTE') or die("Access Denied.");
class TestFormExternalFormBlockController extends BlockController {

	public function action_test_search() {
		
		$this->set('response', t('Thanks!'));
		return true;
		
	}
	
}