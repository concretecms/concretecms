<?php
namespace Concrete\Authentication\Community;

use Concrete\Core\Authentication\Type\Community\Service\Community;
use Concrete\Core\Authentication\Type\OAuth\OAuth2\GenericOauth2TypeController;

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
        return '<i class="fa fa-user"></i>';
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
            $this->service = \Core::make('community_service');
        }
        return $this->service;
    }

    public function saveAuthenticationType($args)
    {
        \Config::save('authentication::community.appid', $args['apikey']);
        \Config::save('authentication::community.secret', $args['apisecret']);
    }

    public function edit()
    {
        $this->set('form', \Loader::helper('form'));
        $this->set('apikey', \Config::get('authentication::community.appid', ''));
        $this->set('apisecret', \Config::get('authentication::community.secret', ''));
    }

    /**
     * @return Array
     */
    public function getAdditionalRequestParameters() {
        return array('state' => time());
    }

    public function getExtractor() {
        try {
            return parent::getExtractor();
        } catch (\Exception $e) {
            dd($e);
        }
    }

}
