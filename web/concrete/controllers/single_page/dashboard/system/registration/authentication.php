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
            self::ERROR_INVALID_TYPE => t('Invalid Authentication Type')
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
        for ($i = 0; $i < $l; $i++) {
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
        try {
            $at = AuthenticationType::getByID($atid);

            /** @var Token $token */
            $token = \Core::make('token');

            if (!$token->validate("auth_type_toggle.{$atid}")) {
                Session::set('authenticationTypesErrorCode', self::ERROR_INVALID_TOKEN);
                $this->redirect('dashboard/system/registration/authentication/');
                exit;
            }

            $at->enable();
            $this->set('message', t('The %s authentication type has been enabled.', $at->getAuthenticationTypeName()));
        } catch (Exception $e) {
            $this->error->add($e->getMessage());
        }
        $this->view();
    }

    public function disable($atid)
    {
        try {
            $at = AuthenticationType::getByID($atid);

            /** @var Token $token */
            $token = \Core::make('token');

            if (!$token->validate("auth_type_toggle.{$atid}")) {
                Session::set('authenticationTypesErrorCode', self::ERROR_INVALID_TOKEN);
                $this->redirect('dashboard/system/registration/authentication/');
                exit;
            }

            $at->disable();
            $this->set('message', t('The %s authentication type has been disabled.', $at->getAuthenticationTypeName()));
        } catch (Exception $e) {
            $this->error->add($e->getMessage());
        }
        $this->view();
    }

    public function save($atid)
    {
        $values = $this->post();
        try {
            $at = AuthenticationType::getByID($atid);

            /** @var Token $token */
            $token = \Core::make('token');

            if (!$token->validate("auth_type_save.{$atid}")) {
                Session::set('authenticationTypesErrorCode', self::ERROR_INVALID_TOKEN);
                $this->redirect('dashboard/system/registration/authentication/');
                exit;
            }

            try {
                $at->controller->saveAuthenticationType($values);
                $this->set('message',
                    t('The %s authentication type has been saved.', $at->getAuthenticationTypeName()));
            } catch (Exception $e) {
                $this->error->add($e->getMessage());
            }
        } catch (Exception $e) {
            Session::set('authenticationTypesErrorCode', self::ERROR_INVALID_TYPE);
            $this->redirect('dashboard/system/registration/authentication/');
            exit;
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
            exit;
        }
        $this->set('at', $at);
        $this->set('editmode', true);
    }
}
