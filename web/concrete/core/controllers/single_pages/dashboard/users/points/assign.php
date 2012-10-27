<?php

class Concrete5_Controller_Dashboard_Users_Points_Assign extends DashboardBaseController {
	
	public $helpers = array('form','concrete/interface', 'concrete/urls', 'image', 'concrete/asset_library', 'form/user_selector', 'form/date_time');	
	protected $upe;
	
	public function __construct() {
		parent::__construct();
		$this->upe = new UserPointEntry();
	}
	
	
	public function on_start() {
		$html = Loader::helper('html');
	}
	
	
	public function view($upID = NULL) {
		
		if(isset($upID) && $upID > 0) {
			$this->upe->load($upID);
			$this->setAttribs($this->upe);
			
			$u = $this->upe->getUserPointEntryUserObject();
			if(is_object($u) && $u->getUserID() > 0) {
				$this->set('upUser',$u->getUserName());
			}
		}
		
		$this->set('userPointActions',$this->getUserPointActions());
	}
	
	protected function setAttribs($upe) {
		$attribs = $upe->getAttributeNames();
		foreach($attribs as $key) {
			$this->set($key, $upe->$key);
		}
	}

	public function save() {
		$error = Loader::helper('validation/error');
		
		if($this->post('upID') > 0) { // load it up if we're editing
			$this->upe->load($this->post('upID'));
		}
		
		$attribs = $this->upe->getAttributeNames();
		foreach($attribs as $key) {
			$val =  $this->post($key);
			if(isset($val)) {
				$this->upe->{$key} = $val;
			}
		}
		
		if($this->post('manual_datetime') > 0) {
			$dt = Loader::helper('form/date_time');
			$this->upe->timestamp = $dt->translate('dtoverride');
		} 
		
		
		
		$user = $this->post('upUser');
		if(is_numeric($user)) {
			// rolling as user id
			$ui = UserInfo::getByUserID($user);
		} else {
			$ui = UserInfo::getByUserName($user); 
			// look up userID
		}
		if($ui && $ui->getUserID()) {
			$this->upe->upuID = $ui->getUserID();
		}
		
		if(!is_numeric($this->upe->upuID) || $this->upe->upuID <= 0) { $error->add('valid user required'); }
		if(!is_numeric($this->upe->upaID) || $this->upe->upaID <= 0) { $error->add('action required'); }
		if(!is_numeric($this->upe->upPoints)) { $error->add('points required'); }
		if(!$error->has()) {
			$this->upe->save();
			$this->redirect('/dashboard/users/points/assign','entry_saved');
		}else{
			$this->set('error',$error);
			$this->view();
		}
		
		
	}

	public function getUserPointActions($typeID = NULL) {
		Loader::model('user_point/action_list');
		$res = array(0=>t('-- None --'));
		$upal = new UserPointActionList();		
		if(isset($typeID) && $typeID > 0) {
			$upal->filterByType($typeID);
		}
		$userPointActions = $upal->get(0);
		if(is_array($userPointActions) && count($userPointActions)) {
			foreach($userPointActions as $upa) {
				$res[$upa['upaID']] = $upa['upaDefaultPoints']." - ".$upa['upaName']; 
			}
		}
		return $res;
	}
	
	
	public function getJsonActionSelectOptions($typeID) {
		$actions = $this->getUserPointActions($typeID);
		$res = array();
		foreach($actions as $key=>$value) {
			$res[] = array('optionValue'=>$key,'optionDisplay'=>$value);
		}
		echo json_encode($res);
		exit;
	}
	
	
	public function getJsonDefaultPointAction($upaID) {
		$upa = new UserPointAction();
		$upa->load($upaID);
		echo json_encode($upa->getUserPointActionDefaultPoints());
		exit;
	}
	
	
	public function entry_saved() {
		$this->set('message',t('User Point Entry Saved'));
		$this->view();
	}
	
}