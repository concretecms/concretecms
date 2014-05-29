<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use AuthenticationType;
use Exception;
use Session;

class Authentication extends DashboardPageController
{
    public function view() {
        $ats = AuthenticationType::getList(true);
        $this->set('ats', $ats);
        if (Session::has('authenticationTypesErrorCode')) {
            $atec = Session::get('authenticationTypesErrorCode');
            Session::remove('authenticationTypesErrorCode');
            $errors = array();
            $errors[0] = t('Invalid Error Code');
            $errors[1] = t('Invalid Authentication Type');
            if (!isset($errors[$atec])) {
                $atec = 0;
            }
            $this->error->add($errors[$atec]);
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
            try {
                $at->controller->saveAuthenticationType($values);
                $this->set('message', t('The %s authentication type has been saved.', $at->getAuthenticationTypeName()));
            } catch (Exception $e) {
                $this->error->add($e->getMessage());
            }
        } catch (Exception $e) {
            Session::set('authenticationTypesErrorCode', 1);
            $this->redirect('dashboard/system/registration/authentication/');
            exit;
        }
        $this->view();
    }

    public function edit($atid) {
        try {
            $at = AuthenticationType::getByID($atid);
        } catch (Exception $e) {
            Session::set('authenticationTypesErrorCode', 1);
            $this->redirect('dashboard/system/registration/authentication/');
            exit;
        }
        $this->set('at', $at);
        $this->set('editmode', true);
    }
}
