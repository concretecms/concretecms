<?php
namespace Concrete\Authentication\Twitter;

defined('C5_EXECUTE') or die('Access Denied');

use Concrete\Core\Authentication\Type\OAuth\OAuth1a\GenericOauth1aTypeController;
use OAuth\OAuth1\Service\Twitter;

class Controller extends GenericOauth1aTypeController
{

    /**
     * Twitter doesn't give us the users email.
     *
     * @return bool
     */
    public function supportsRegistration()
    {
        return false;
    }

    /**
     * Twitter doesn't give us the users email.
     *
     * @return bool
     */
    public function supportsEmailResolution()
    {
        return false;
    }

    public function getAuthenticationTypeIconHTML()
    {
        return '<i class="fa fa-twitter"></i>';
    }

    public function getHandle()
    {
        return 'twitter';
    }

    /**
     * @return Twitter
     */
    public function getService()
    {
        if (!$this->service) {
            $this->service = \Core::make('twitter_service');
        }
        return $this->service;
    }

    public function saveAuthenticationType($args)
    {
        \Config::save('authentication::twitter.appid', $args['apikey']);
        \Config::save('authentication::twitter.secret', $args['apisecret']);
    }

    public function edit()
    {
        $this->set('form', \Loader::helper('form'));
        $this->set('apikey', \Config::get('authentication::twitter.appid', ''));
        $this->set('apisecret', \Config::get('authentication::twitter.secret', ''));
    }

}
