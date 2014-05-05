<?
namespace Concrete\Core\Page\Controller;
use Loader;
class DashboardPageController extends PageController {
	
	protected $error; 
	public $token;
	protected $helpers = array('form');

	public function enableNativeMobile() {
		$md = new \Mobile_Detect();
		if ($md->isMobile()) {
			$this->addHeaderItem('<meta name="viewport" content="width=device-width,initial-scale=1"/>');
		}
	}
	
	public function on_start() {
		$this->token = Loader::helper('validation/token');
		$this->error = Loader::helper('validation/error');
		$this->set('interface', Loader::helper('concrete/ui'));
		$this->set('dashboard', Loader::helper('concrete/dashboard'));
	}
	
	public function on_before_render() {
		$pageTitle = $this->get('pageTitle');
		if (!$pageTitle) {
			$this->set('pageTitle', $this->c->getCollectionName());
		}
		$this->set('token', $this->token);
		$this->set('error', $this->error);
	}


}
