<?php
namespace Concrete\Authentication\Facebook;

defined('C5_EXECUTE') or die('Access Denied');

use Concrete\Core\Authentication\Type\OAuth\OAuth2\GenericOauth2TypeController;
use OAuth\OAuth2\Service\Facebook;

class Controller extends GenericOauth2TypeController
{

    public function registrationGroupID()
    {
        return \Config::get('auth.facebook.registration.group');
    }

    public function supportsRegistration()
    {
        return \Config::get('auth.facebook.registration.enabled', false);
    }

    public function getAuthenticationTypeIconHTML()
    {
        return '<i class="fa fa-facebook"></i>';
    }

    public function getHandle()
    {
        return 'facebook';
    }

    /**
     * @return Facebook
     */
    public function getService()
    {
        if (!$this->service) {
            $this->service = \Core::make('authentication/facebook');
        }
        return $this->service;
    }

    public function saveAuthenticationType($args)
    {
        \Config::save('auth.facebook.appid', $args['apikey']);
        \Config::save('auth.facebook.secret', $args['apisecret']);
        \Config::save('auth.facebook.registration.enabled', !!$args['registration_enabled']);
        \Config::save('auth.facebook.registration.group', intval($args['registration_group'], 10));
    }

    public function edit()
    {
        $this->set('form', \Loader::helper('form'));
        $this->set('apikey', \Config::get('auth.facebook.appid', ''));
        $this->set('apisecret', \Config::get('auth.facebook.secret', ''));

        $list = new \GroupList();
        $list->includeAllGroups();
        $this->set('groups', $list->getResults());
    }

}
