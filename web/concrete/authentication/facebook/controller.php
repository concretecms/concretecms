<?php
namespace Concrete\Authentication\Facebook;

defined('C5_EXECUTE') or die('Access Denied');

use Concrete\Core\Authentication\Type\OAuth\OAuth2\GenericOauth2TypeController;
use OAuth\OAuth2\Service\Facebook;

class Controller extends GenericOauth2TypeController
{

    public function supportsRegistration()
    {
        return true;
    }

    public function supportsEmailResolution()
    {
        return true;
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
        \Config::save('authentication::facebook.appid', $args['apikey']);
        \Config::save('authentication::facebook.secret', $args['apisecret']);
    }

    public function edit()
    {
        $this->set('form', \Loader::helper('form'));
        $this->set('apikey', \Config::get('authentication::facebook.appid', ''));
        $this->set('apisecret', \Config::get('authentication::facebook.secret', ''));
    }

}
