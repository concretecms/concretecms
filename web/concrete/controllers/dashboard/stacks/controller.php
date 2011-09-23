<?
defined('C5_EXECUTE') or die("Access Denied.");

Loader::controller('/dashboard/base');
class DashboardStacksController extends DashboardBaseController {

	
	public function on_start() {
		parent::on_start();
		Loader::model('stack/list');
		$stm = new StackList();
		$this->set('stacks', $stm->get());
	}		
	
	public function add_stack() {
		if (Loader::helper('validation/token')->validate('add_stack')) {
			$stack = Stack::addStack($this->post('stackName'));
			$this->redirect('/dashboard/stacks', 'stack_added');
		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
	}
	
	public function stack_added() {
		$this->set('message', t('Stack added successfully'));
	}
	
	public function view_details($cID) {
		$this->addHeaderItem(Loader::helper('html')->css('ccm.ui.css'));
		$s = Stack::getByID($cID);
		if (is_object($s)) {
			$blocks = $s->getBlocks('Main');
			$view = View::getInstance();
			foreach($blocks as $b1) {
				$btc = $b1->getInstance();
				// now we inject any custom template CSS and JavaScript into the header
				if('Controller' != get_class($btc)){
					$btc->outputAutoHeaderItems();
				}
				$btc->runTask('on_page_view', array($view));
			}
			$this->addHeaderItem('<style type="text/css">' . $s->outputCustomStyleHeaderItems(true) . '</style>');

			$this->set('stack', $s);
			$this->set('blocks', $blocks);
		} else {
			throw new Exception(t('Invalid stack'));
		}
	}
	
}