<?php
namespace Concrete\Authentication\Facebook;

defined('C5_EXECUTE') or die('Access Denied');

use Concrete\Core\Authentication\Type\OAuth\OAuth2\GenericOauth2TypeController;
use OAuth\OAuth2\Service\Facebook;

class Controller extends GenericOauth2TypeController
{

    public function supportsRegistration()
    {
        return \Config::get('auth.facebook.registration_enabled', false);
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
            $this->service = \Core::make('facebook_service');
        }
        return $this->service;
    }

    public function saveAuthenticationType($args)
    {
        \Config::save('auth.facebook.appid', $args['apikey']);
        \Config::save('auth.facebook.secret', $args['apisecret']);
        \Config::save('auth.facebook.registration_enabled', $args['registration_enabled']);
    }

    public function edit()
    {
        $this->set('form', \Loader::helper('form'));
        $this->set('apikey', \Config::get('auth.facebook.appid', ''));
        $this->set('apisecret', \Config::get('auth.facebook.secret', ''));
    }

}
