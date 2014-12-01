<?php
namespace Concrete\Authentication\Google;

defined('C5_EXECUTE') or die('Access Denied');

use Concrete\Core\Authentication\LoginException;
use Concrete\Core\Authentication\Type\OAuth\OAuth2\GenericOauth2TypeController;
use OAuth\OAuth2\Service\Google;
use User;

class Controller extends GenericOauth2TypeController
{

    public function supportsRegistration()
    {
        return \Config::get('auth.google.registration_enabled', false);
    }

    public function getAuthenticationTypeIconHTML()
    {
        return '<i class="fa fa-google"></i>';
    }

    public function getHandle()
    {
        return 'google';
    }

    /**
     * @return Google
     */
    public function getService()
    {
        if (!$this->service) {
            $this->service = \Core::make('google_service');
        }
        return $this->service;
    }

    public function saveAuthenticationType($args)
    {
        \Config::save('auth.google.appid', $args['apikey']);
        \Config::save('auth.google.secret', $args['apisecret']);
        \Config::save('auth.google.registration_enabled', $args['registration_enabled']);


        $whitelist = array();
        foreach (explode(PHP_EOL, $args['whitelist']) as $entry) {
            $whitelist[] = $entry;
        }

        $blacklist = array();
        foreach (explode(PHP_EOL, $args['blacklist']) as $entry) {
            $blacklist[] = json_decode($entry, true);
        }

        \Config::save('auth.google.email_filters.whitelist', $whitelist);
        \Config::save('auth.google.email_filters.blacklist', $blacklist);
    }

    public function edit()
    {
        $this->set('form', \Loader::helper('form'));
        $this->set('apikey', \Config::get('auth.google.appid', ''));
        $this->set('apisecret', \Config::get('auth.google.secret', ''));

        $this->set('whitelist', \Config::get('auth.google.email_filters.whitelist', array()));
        $blacklist = array_map(function($entry) {
            return json_encode($entry);
        }, \Config::get('auth.google.email_filters.blacklist', array()));

        $this->set('blacklist', $blacklist);
    }

    public function completeAuthentication(User $u)
    {
        $ui = \UserInfo::getByID($u->getUserID());
        if (!$ui->hasAvatar()) {
            $image = \Image::open($this->getExtractor()->getImageURL());
            $ui->updateUserAvatar($image);
        }

        parent::completeAuthentication($u);
    }

    public function isValid()
    {
        $filters = (array)\Config::get('auth.google.email_filters', array());
        $domain = $this->getExtractor()->getExtra('domain');

        foreach (array_get($filters, 'whitelist', array()) as $regex) {
            if (preg_match($regex, $domain)) {
                return true;
            }
        }

        foreach (array_get($filters, 'blacklist', array()) as $arr) {
            list($regex, $error) = array_pad((array) $arr, 2, null);
            if (preg_match($regex, $domain)) {
                if (trim($error)) {
                    throw new LoginException($error);
                }
                return false;
            }
        }

        return true;
    }

}
