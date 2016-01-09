<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;

class Profiles extends DashboardPageController {

	public $helpers = array('form');

	public function update_profiles() {

        /** @var Token $token */
        $token = \Core::make('token');

        if (!$token->validate("update_profile")) {
            $this->error->add('Invalid Token.');
            return;
        }

		if ($this->isPost()) {
			Config::save('concrete.user.profiles_enabled', ($this->post('public_profiles')?true:false));
			Config::save('concrete.user.gravatar.enabled', ($this->post('gravatar_fallback')?true:false));
			Config::save('concrete.user.gravatar.max_level', Loader::helper('security')->sanitizeString($this->post('gravatar_max_level')));
			Config::save('concrete.user.gravatar.image_set', Loader::helper('security')->sanitizeString($this->post('gravatar_image_set')));
			// $message = ($this->post('public_profiles')?t('Public profiles have been enabled'):t('Public profiles have been disabled.'));
            if($this->post('public_profiles')) {
			    $this->redirect('/dashboard/system/registration/profiles/profiles_enabled');
            } else {
                $this->redirect('/dashboard/system/registration/profiles/profiles_disabled');
            }
		}
	}

    public function profiles_enabled(){
        $this->set('message', t('Public profiles have been enabled'));
        $this->view();
    }

    public function profiles_disabled(){
        $this->set('message', t('Public profiles have been disabled'));
        $this->view();
    }

	public function view($message = NULL) {
		if($message) {
			$this->set('message',$message);
		}
    $this->token = Loader::helper('validation/token');

    $this->set('public_profiles',Config::get('concrete.user.profiles_enabled'));
    $this->set('gravatar_fallback', Config::get('concrete.user.gravatar.enabled'));
    $this->set('gravatar_max_level', Config::get('concrete.user.gravatar.max_level'));
    $this->set('gravatar_level_options', array('g' => 'G', 'pg' => 'PG', 'r' => 'R', 'x' => 'X'));
    $this->set('gravatar_image_set', Config::get('concrete.user.gravatar.image_set'));
    $this->set('gravatar_set_options', array('404' => '404', 'mm' => 'mm', 'identicon' => 'identicon', 'monsterid' => 'monsterid', 'wavatar' => "wavatar"));
	}
}
