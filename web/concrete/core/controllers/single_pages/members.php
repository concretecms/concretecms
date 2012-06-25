<?php
defined('C5_EXECUTE') or die("Access Denied."); 

class Concrete5_Controller_Members extends Controller {

	public function view() {
		$userList = new UserList(); 
		$userList->sortBy('uName', 'asc'); 
		$keywords = $this->get('keywords');
		if ($keywords != '') {
			$userList->filterByKeywords($keywords);
		}
		$users = $userList->getPage();
		$this->set('userList', $userList);						
		$this->set('users', $users);
		$this->set('attribs', UserAttributeKey::getMemberListList());
		$this->set('keywords', htmlentities($keywords, ENT_COMPAT, APP_CHARSET));
		$this->addHeaderItem(Loader::helper('html')->css('ccm.profile.css'));
	}

}
	
?>