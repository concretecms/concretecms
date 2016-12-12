<?php
namespace Concrete\Authentication\Google;

defined('C5_EXECUTE') or die('Access Denied');

use Concrete\Core\Authentication\LoginException;
use Concrete\Core\Authentication\Type\Google\Factory\GoogleServiceFactory;
use Concrete\Core\Authentication\Type\OAuth\OAuth2\GenericOauth2TypeController;
use OAuth\OAuth2\Service\Google;
use Concrete\Core\User\User;

class Controller extends GenericOauth2TypeController
{
    public function supportsRegistration()
    {
        return \Config::get('auth.google.registration.enabled', false);
    }

    public function registrationGroupID()
    {
        return \Config::get('auth.google.registration.group');
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
            /** @var GoogleServiceFactory $factory */
            $factory = $this->app->make(GoogleServiceFactory::class);
            $this->service = $factory->createService();
        }

        return $this->service;
    }

    public function saveAuthenticationType($args)
    {
        \Config::save('auth.google.appid', $args['apikey']);
        \Config::save('auth.google.secret', $args['apisecret']);
        \Config::save('auth.google.registration.enabled', (bool) $args['registration_enabled']);
        \Config::save('auth.google.registration.group', intval($args['registration_group'], 10));

        $whitelist = array();
        foreach (explode(PHP_EOL, $args['whitelist']) as $entry) {
            $whitelist[] = trim($entry);
        }

        $blacklist = array();
        foreach (explode(PHP_EOL, $args['blacklist']) as $entry) {
            $blacklist[] = json_decode(trim($entry), true);
        }

        \Config::save('auth.google.email_filters.whitelist', array_values(array_filter($whitelist)));
        \Config::save('auth.google.email_filters.blacklist', array_values(array_filter($blacklist)));
    }

    public function edit()
    {
        $this->set('form', \Loader::helper('form'));
        $this->set('apikey', \Config::get('auth.google.appid', ''));
        $this->set('apisecret', \Config::get('auth.google.secret', ''));

        $list = new \GroupList();
        $list->includeAllGroups();
        $this->set('groups', $list->getResults());

        $this->set('whitelist', \Config::get('auth.google.email_filters.whitelist', array()));
        $blacklist = array_map(function ($entry) {
            return json_encode($entry);
        }, \Config::get('auth.google.email_filters.blacklist', array()));

        $this->set('blacklist', $blacklist);
    }

    public function completeAuthentication(User $u)
    {
        $ui = \UserInfo::getByID($u->getUserID());
        if (!$ui->hasAvatar()) {
            try {
                $image = \Image::open($this->getExtractor()->getImageURL());
                $ui->updateUserAvatar($image);
            } catch (\Imagine\Exception\InvalidArgumentException $e) {
                \Log::addNotice("Unable to fetch user images in Google Authentication Type, is allow_url_fopen disabled?");
            } catch (\Exception $e) {
            }
        }

        return parent::completeAuthentication($u);
    }

    public function isValid()
    {
        $filters = (array) \Config::get('auth.google.email_filters', array());
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
