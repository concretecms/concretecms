<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Users_Groups extends DashboardBaseController {


	public function view() { 
	
	}	
	
	public function update_group() {
		$g = Group::getByID(intval($_REQUEST['gID']));
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
		
		$g1 = Group::getByName($gName);
		if ($g1 instanceof Group) {
			if ((!is_object($g)) || $g->getGroupID() != $g1->getGroupID()) {
				$this->error->add(t('A group named "%s" already exists', $g1->getGroupName()));
			}
		}
		
		if (count($error) == 0) {
			$g->update($gName, $_POST['gDescription']);
			$cnta = Loader::controller('/dashboard/users/add_group');
			$cnta->checkExpirationOptions($g);
			$this->redirect('/dashboard/users/groups', 'group_updated');
		}	
	}
	
	public function group_added() {
		$this->set('message', t('Group added successfully'));
	}
	
	public function group_updated() {
		$this->set('message', t('Group update successfully'));
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
			
			$_REQUEST=array();
			$_GET=array();
			$_POST=array();			
			$this->set('message', $resultMsg);
			$this->view(); 
		} catch(Exception $e) {
			$this->set('error', $e);
		}
	}	
}

?>