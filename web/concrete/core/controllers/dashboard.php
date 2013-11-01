<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard extends PageController {
	
	protected $error; 
	public $token;
	protected $helpers = array('form');

	public function enableNativeMobile() {
		Loader::library('3rdparty/mobile_detect');
		$md = new Mobile_Detect();
		if ($md->isMobile()) {
			$this->addHeaderItem('<meta name="viewport" content="width=device-width,initial-scale=1"/>');
		}
	}
	
	public function on_start() {
		$this->token = Loader::helper('validation/token');
		$this->error = Loader::helper('validation/error');
		$this->set('interface', Loader::helper('concrete/interface'));
	}
	
	public function on_before_render() {
		$pageTitle = $this->get('pageTitle');
		if (!$pageTitle) {
			$this->set('pageTitle', $this->c->getCollectionName());
		}
		$this->set('error', $this->error);
	}


}