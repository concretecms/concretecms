<?
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardBaseController extends Controller {
	
	protected $error; 
	public $token;
	public $helpers = array('form');
	
	public function on_start() {
		$this->token = Loader::helper('validation/token');
		$this->error = Loader::helper('validation/error');
		$this->set('interface', Loader::helper('concrete/interface'));
	}
	
	public function on_before_render() {
		$this->set('error', $this->error);
	}


}