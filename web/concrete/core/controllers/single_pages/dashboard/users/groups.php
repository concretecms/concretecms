<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Users_Groups extends DashboardBaseController {


	public function view() { 
		$tree = GroupTree::get();
		$this->set('tree', $tree);

		$this->addHeaderItem(Loader::helper('html')->css('dynatree/dynatree.css'));
		$this->addFooterItem(Loader::helper('html')->javascript('dynatree/dynatree.js'));

		if (isset($_GET['gKeywords'])) {
			$gKeywords = Loader::helper('security')->sanitizeString($_GET['gKeywords']);
		}

		$tp = new Permissions();
		if ($_REQUEST['group_submit_search'] && $gKeywords && $tp->canAccessGroupSearch()) {

			$gl = new GroupSearch();
			$gl->filterByKeywords($gKeywords);
			$gResults = $gl->getPage();
			$results = array();
			foreach($gResults as $gRow) {
				$g = Group::getByID($gRow['gID']);
				if (is_object($g)) {
					$results[] = $g;
				}
			}

			$this->set('results', $results);
			$this->set('groupList', $gl);

		}
	}	

	public function edit($gID = false) {
		$g = Group::getByID(intval($gID));
		$gp = new Permissions($g);
		if (!$gp->canEditGroup()) {
			throw new Exception(t('You do not have access to edit this group.'));
		}
		if (is_object($g)) { 		
			$this->set('group', $g);
		}		
	}

	public function bulk_update_complete() {
		$this->set('success', t('Groups moved successfully.'));
		$this->view();
	}

	public function update_group() {
		$g = Group::getByID(intval($_REQUEST['gID']));
		if (is_object($g)) {
			$this->set('group', $g);
		}
		$gp = new Permissions($g);
		if (!$gp->canEditGroup()) {
			$this->error->add(t('You do not have access to edit this group.'));
		}

		$txt = Loader::helper('text');
		$valt = Loader::helper('validation/token');
		$gName = $txt->sanitize($_POST['gName']);
		$gDescription = $_POST['gDescription'];
		
		if (!$gName) {
			$this->error->add(t("Name required."));
		}
		
		if (!$valt->validate('add_or_update_group')) {
			$this->error->add($valt->getErrorMessage());
		}
		
		if (!$this->error->has()) {
			$g->update($gName, $_POST['gDescription']);
			$cnta = Loader::controller('/dashboard/users/add_group');
			$cnta->checkExpirationOptions($g);
			$this->redirect('/dashboard/users/groups', 'group_updated');
		}
	}
	
	public function group_added() {
		$this->set('message', t('Group added successfully'));
		$this->view();
	}
	
	public function group_updated() {
		$this->set('message', t('Group update successfully'));
		$this->view();
	}
	
	public function delete($delGroupId, $token = ''){

		$u=new User();
		try {
		
			if(!$u->isSuperUser()) {
				throw new Exception(t('You do not have permission to perform this action.'));
			}
			
			$group = Group::getByID($delGroupId);			
			
			if(!($group instanceof Group)) {
				throw new Exception(t('Invalid group ID.'));
			}
			
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('delete_group_' . $delGroupId, $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$group->delete(); 
			$resultMsg=t('Group deleted successfully.');		
			$this->set('message', $resultMsg);
		} catch(Exception $e) {
			$this->error->add($e);
		}
		$this->view(); 
	}	
}

?>