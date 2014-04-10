<?php
namespace \Concrete\Controller\SinglePage\Account\Members;
use \Concrete\Core\Page\Controller\AccountPageController;
use UserAttributeKey;
use Loader;
use UserList;

class Directory extends AccountPageController {

	public function on_start() {
		parent::on_start();
		$this->requireAsset('core/frontend/pagination');
		$this->userList = new UserList(); 
		$this->userList->sortBy('uName', 'asc'); 
	}
	
	public function on_before_render() {
		$users = $this->userList->getPage();
		$this->set('userList', $this->userList);						
		$this->set('users', $users);
		$this->set('attribs', UserAttributeKey::getMemberListList());
		$this->set('keywords', htmlentities($keywords, ENT_COMPAT, APP_CHARSET));
		$this->set('keywords', Loader::helper('text')->entities($_REQUEST['keywords']));
	}	
	
	public function search_members() {
		$keywords = $this->get('keywords');
		if ($keywords != '') {
			$this->userList->filterByKeywords($keywords);
		}
	}

}