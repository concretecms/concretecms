<?
namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;

class Postlogin extends DashboardPageController {

	public $helpers = array('form'); 
	
	public function __construct( $c ) {
		parent::__construct( $c );
		$this->token = Loader::helper('validation/token');

		//login redirection
		$this->set('site_login_redirect', Config::get('LOGIN_REDIRECT') );
		$this->set('login_redirect_cid', intval(Config::get('LOGIN_REDIRECT_CID')) ); 
		$adminToDash=Config::get('LOGIN_ADMIN_TO_DASHBOARD');
		$this->set('site_login_admin_to_dashboard', intval($adminToDash) );		
	}

	public function update_login_redirect(){ 
		if ($this->token->validate("update_login_redirect")) {	
			if ($this->isPost()) {
				Config::save('LOGIN_REDIRECT', $this->post('LOGIN_REDIRECT'));
				Config::save('LOGIN_REDIRECT_CID', intval($this->post('LOGIN_REDIRECT_CID')) );
				Config::save('LOGIN_ADMIN_TO_DASHBOARD', intval($this->post('LOGIN_ADMIN_TO_DASHBOARD')) );
				
				$this->redirect( '/dashboard/system/registration/postlogin', 'login_redirect_saved');
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}	
	}
	
	public function view($message = NULL) {
		if($message) {
			if($message=='login_redirect_saved'){
				$this->set('message', t('Login redirection saved.'));
			}else{
				$this->set('message',$message);
			}
		}
	}
}
