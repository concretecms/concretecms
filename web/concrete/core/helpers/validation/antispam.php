<?php

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Validation_Antispam {
	
	protected $controller = false;
	
	public function __construct() {
		Loader::model('system/antispam/library');
		$library = SystemAntispamLibrary::getActive();
		if (is_object($library)) { 
			$this->controller = $library->getController();
		}
	}		
	
	public function check($content, $type, $additionalArgs = array()) {
		if ($this->controller) { 
			$args['ip_address'] = Loader::helper('validation/ip')->getRequestIP();
			$args['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
			$args['content'] = $content;
			foreach($additionalArgs as $key => $value) {
				$args[$key] = $value;
			}
			if (isset($args['user']) && is_object($args['user'])) {
				$u = $args['user'];
			} else {
				$u = new User();
			}
			if (!isset($args['email']) && $u->isRegistered()) {
				$ui = UserInfo::getByID($u->getUserID());
				$args['email'] = $ui->getUserEmail();
			}
			$r = $this->controller->check($args);
			if ($r) {
				return true;
			} else {
				$c = Page::getCurrentPage();
				if (is_object($c)) { 
					$logText .= t('URL: %s', Loader::helper('navigation')->getLinkToCollection($c, true));
					$logText .= "\n";
				}
				if ($u->isRegistered()) { 
					$logText .= t('User: %s (ID %s)', $u->getUserName(), $u->getUserID());
					$logText .= "\n";
				}
				$logText .= t('Type: %s', Loader::helper('text')->unhandle($type));
				$logText .= "\n";
				foreach($args as $key => $value) {
					$logText .= Loader::helper('text')->unhandle($key) . ': ' . $value . "\n";
				}
				
				if (Config::get('ANTISPAM_LOG_SPAM')) {
					Log::addEntry($logText, t('spam'));
				}
				if (Config::get('ANTISPAM_NOTIFY_EMAIL') != '') {
					$mh = Loader::helper('mail');
					$mh->to(Config::get('ANTISPAM_NOTIFY_EMAIL'));
					$mh->addParameter('content', $logText);
					$mh->load('spam_detected');
					$mh->sendMail();
				}
				return false;
			}
		} else {
			return true; // return true if it passes the test
		}
	}
	
	public function __call($nm, $args) {
		if (method_exists($this->controller, $nm)) { 
			return call_user_func_array(array($this->controller, $nm), $args);
		}
	}
	
	
}