<?php
class Concrete5_Controller_Dashboard_Users_Points_Actions extends DashboardBaseController {
	public $helpers = array('form','concrete/interface', 'concrete/urls', 'image', 'concrete/asset_library');
	public $upa;
		
	
	public function __construct() {
		parent::__construct();
		$this->upa = new UserPointAction();
	}
	
	
	public function add() {
		$this->set('add_edit',t('Add'));
		$this->set('showForm',true);
		$this->view();
	}
	
	
	public function view($upaID=NULL) {
		
		if(isset($upaID)) {
			$this->set('add_edit',t('Edit'));
			$this->upa->load($upaID);		
			$this->setAttribs($this->upa);
			$g = $this->upa->getUserPointActionBadgeGroupObject();
			if(is_object($g)) {
				$this->set('upaBadgeGroupName',$g->getGroupName());
			}
			$this->set('showForm',true);
		}
		
		$actionList = $this->getActionList();
		$this->set('pagination',$actionList->getPagination());
		$this->set('actionList',$actionList);
		$this->set('actions',$actionList->get());
	}
	
	
	public function getActionList() {
		$al = new UserPointActionList();
		
		switch($_REQUEST['ccm_order_by']) {
			case 'groupName':
				$al->sortBy('Groups.groupName', $_REQUEST['ccm_order_dir']);
			break;
			case 'upaDefaultPoints':
				$al->sortBy('upaDefaultPoints', $_REQUEST['ccm_order_dir']);
			break;
			case 'upaHandle':
				$al->sortBy('upaHandle', $_REQUEST['ccm_order_dir']);
			break;
			case 'upaName':
				$al->sortBy('upaName', $_REQUEST['ccm_order_dir']);
			break;
			case 'upaTypeID':
				$al->sortBy('upaTypeID', $_REQUEST['ccm_order_dir']);
			break;
			default:
				$al->sortBy('upaName','ASC');
			break;
		}
		return $al;
	}
	
	
	protected function setAttribs($upa) {
		$attribs = $upa->getAttributeNames();
		foreach($attribs as $key) {
			$this->set($key, $upa->$key);
		}
	}
	
	
	public function save() {
		if($this->post('upaID') > 0) {
			$this->upa->load($this->post('upaID'));
		}
		$attribs = $this->upa->getAttributeNames();
		foreach($attribs as $key) {
			$this->upa->{$key} = $this->post($key);
		}
		$this->upa->save();
		
		$this->redirect('/dashboard/users/points/actions','action_saved');
	}	
	
	
	public function delete($upaID) {
		$this->upa->load($upaID);
		$this->upa->delete();
		$this->redirect('/dashboard/users/points/actions','action_deleted');
	}
	
	
	public function action_deleted() {
		$this->set('message','User Point Action Deleted');
		$this->view();
	} 
	
	
	public function action_saved() {
		$this->set('message','User Point Action Saved');
		$this->view();
	}

}