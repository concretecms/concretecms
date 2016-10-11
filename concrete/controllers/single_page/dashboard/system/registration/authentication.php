<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use AuthenticationType;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Validation\CSRF\Token;
use Exception;
use Session;

class Authentication extends DashboardPageController
{
    const ERROR_INVALID_TYPE = 1;
    const ERROR_INVALID_TOKEN = 2;

    public function getErrorStrings()
    {
        return array(
            self::ERROR_INVALID_TOKEN => t('Invalid Token'),
            self::ERROR_INVALID_TYPE => t('Invalid Authentication Type'),
        );
    }

    public function getErrorString($error)
    {
        return array_get($this->getErrorStrings(), $error, t('Invalid Error Code'));
    }

    public function view()
    {
        $ats = AuthenticationType::getList(true);
        $this->set('ats', $ats);
        if (Session::has('authenticationTypesErrorCode')) {
            $atec = Session::get('authenticationTypesErrorCode');
            Session::remove('authenticationTypesErrorCode');

            $this->error->add($this->getErrorString($atec));
        }
    }

    public function reorder()
    {
        $order = $this->post('order');
        $l = count($order);
        for ($i = 0; $i < $l; ++$i) {
            try {
                $at = AuthenticationType::getByID($order[$i]);
                $at->setAuthenticationTypeDisplayOrder($i);
            } catch (exception $e) {
            }
        }
        exit;
    }

    public function enable($atid)
    {
        if (!$this->token->validate("auth_type_toggle.{$atid}")) {
            $this->app->make('session')->set('authenticationTypesErrorCode', self::ERROR_INVALID_TOKEN);
            $this->redirect('dashboard/system/registration/authentication/');
        }
        try {
            $at = AuthenticationType::getByID($atid);
            $at->enable();
            $this->set('message', t('The %s authentication type has been enabled.', $at->getAuthenticationTypeName()));
        } catch (Exception $e) {
            $this->error->add($e->getMessage());
        }
        $this->view();
    }

    public function disable($atid)
    {
        if (!$this->token->validate("auth_type_toggle.{$atid}")) {
            $this->app->make('session')->set('authenticationTypesErrorCode', self::ERROR_INVALID_TOKEN);
            $this->redirect('dashboard/system/registration/authentication/');
        }
        
        try {
            $at = AuthenticationType::getByID($atid);
            $at->disable();
            $this->set('message', t('The %s authentication type has been disabled.', $at->getAuthenticationTypeName()));
        } catch (Exception $e) {
            $this->error->add($e->getMessage());
        }
        $this->view();
    }

    public function save($atid)
    {
        if (!$this->token->validate("auth_type_save.{$atid}")) {
            $this->app->make('session')->set('authenticationTypesErrorCode', self::ERROR_INVALID_TOKEN);
            $this->redirect('dashboard/system/registration/authentication/');
        }
        
        $values = $this->post();
        try {
            $at = AuthenticationType::getByID($atid);
            try {
                $at->controller->saveAuthenticationType($values);
                $this->set('message', t('The %s authentication type has been saved.', $at->getAuthenticationTypeName()));
            } catch (Exception $e) {
                $this->error->add($e->getMessage());
            }
        } catch (Exception $e) {
            $this->app->make('session')->set('authenticationTypesErrorCode', self::ERROR_INVALID_TYPE);
            $this->redirect('dashboard/system/registration/authentication/');
        }
        $this->view();
    }

    public function edit($atid)
    {
        try {
            $at = AuthenticationType::getByID($atid);
        } catch (Exception $e) {
            Session::set('authenticationTypesErrorCode', self::ERROR_INVALID_TYPE);
            $this->redirect('dashboard/system/registration/authentication/');
        }
        $this->set('at', $at);
        $this->set('editmode', true);
    }
}
