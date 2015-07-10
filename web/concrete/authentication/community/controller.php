<?php

namespace Concrete\Authentication\Community;

use Concrete\Core\Authentication\Type\Community\Service\Community;
use Concrete\Core\Authentication\Type\OAuth\OAuth2\GenericOauth2TypeController;
use Core;

class Controller extends GenericOauth2TypeController
{
    public function registrationGroupID()
    {
        return \Config::get('auth.community.registration.group');
    }

    public function supportsRegistration()
    {
        return \Config::get('auth.community.registration.enabled', false);
    }

    public function getAuthenticationTypeIconHTML()
    {
        return '<div class="ccm-concrete-authentication-type-svg" data-src="/concrete/images/authentication/community/concrete.svg">' .
                    file_get_contents(DIR_BASE_CORE . '/images/authentication/community/concrete.svg') .
               '</div>';
    }

    public function getHandle()
    {
        return 'community';
    }

    /**
     * @return Community
     */
    public function getService()
    {
        if (!$this->service) {
            $this->service = \Core::make('authentication/community');
        }

        return $this->service;
    }

    public function saveAuthenticationType($args)
    {
        \Config::save('auth.community.appid', $args['apikey']);
        \Config::save('auth.community.secret', $args['apisecret']);
        \Config::save('auth.community.registration.enabled', !!$args['registration_enabled']);
        \Config::save('auth.community.registration.group', intval($args['registration_group'], 10));
    }

    public function edit()
    {
        $this->set('form', Core::make('helper/form'));
        $this->set('apikey', \Config::get('auth.community.appid', ''));
        $this->set('apisecret', \Config::get('auth.community.secret', ''));

        $list = new \GroupList();
        $list->includeAllGroups();
        $this->set('groups', $list->getResults());
    }

    /**
     * @return Array
     */
    public function getAdditionalRequestParameters()
    {
        return array('state' => time());
    }

    public function getExtractor($new = false)
    {
        try {
            return parent::getExtractor($new);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
