<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Navigation\Item\Item;
use Concrete\Core\Page\Controller\DashboardPageController;
use Exception;

class Authentication extends DashboardPageController
{
    protected const HEADER_ITEM = <<<'EOT'
<style>
#ccm-authentication-types>tbody>tr {
    cursor:pointer;
}
#ccm-authentication-types>tbody>tr>td.ccm-authenticationtype-icon {
    overflow:hidden;
    text-align: center;
    width: 50px;
}
#ccm-authentication-types>tbody>tr>td.ccm-authenticationtype-icon>div {
    height: 15px;
}
#ccm-authentication-types>tbody>tr>td.ccm-authenticationtype-id {
    width: 1px;
    text-align: center;
}
#ccm-authentication-types>tbody i.ccm-authenticationtype-move {
    cursor:move;
}
#ccm-authentication-types .ccm-concrete-authentication-type-svg > svg {
    width:20px;
    display:inline-block;
}
</style>
EOT
    ;

    public function view()
    {
        $this->addHeaderItem(static::HEADER_ITEM);
        $autenticationTypes = AuthenticationType::getList(true);
        $this->set('autenticationTypes', $autenticationTypes);
    }

    public function reorder()
    {
        if (!$this->token->validate('authentication_reorder')) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $authenticationTypeIDs = $this->request->request('order');
        if (!is_array($authenticationTypeIDs)) {
            throw new UserMessageException(t('Invalid data received.'));
        }
        $authenticationTypes = [];
        foreach ($authenticationTypeIDs as $authenticationTypeID) {
            try {
                $authenticationTypes[] = AuthenticationType::getByID($authenticationTypeID);
            } catch (Exception $x) {
                throw new UserMessageException(t('Invalid data received.'));
            }
        }
        foreach ($authenticationTypes as $position => $authenticationType) {
            $authenticationType->setAuthenticationTypeDisplayOrder($position);
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    public function edit($authenticationTypeID = '')
    {
        $authenticationTypeID = (int) $authenticationTypeID;
        try {
            $authenticationType = $authenticationTypeID === 0 ? null : AuthenticationType::getByID($authenticationTypeID);
        } catch (Exception $x) {
            $authenticationType = null;
        }
        if ($authenticationType === null) {
            $this->flash('error', t('Invalid Authentication Type'));

            return $this->buildRedirect($this->action());
        }
        $breadcrumb = $this->createBreadcrumb();
        $breadcrumb->add(new Item('', $authenticationType->getAuthenticationTypeDisplayName(), true));
        $this->setBreadcrumb($breadcrumb);
        $this->set('pageTitle', t('Edit %s Authentication Type', $authenticationType->getAuthenticationTypeDisplayName()));
        $this->set('authenticationType', $authenticationType);
        $this->render('/dashboard/system/registration/authentication/form');
    }

    public function save($authenticationTypeID = '')
    {
        $post = $this->request->request;
        $authenticationTypeID = (int) $authenticationTypeID;
        try {
            $authenticationType = $authenticationTypeID === 0 ? null : AuthenticationType::getByID($authenticationTypeID);
        } catch (Exception $x) {
            $authenticationType = null;
        }
        if ($authenticationType === null) {
            $this->flash('error', t('Invalid Authentication Type'));

            return $this->buildRedirect($this->action());
        }
        if (!$this->token->validate("auth_type_save.{$authenticationTypeID}")) {
            $this->error->add($this->token->getErrorMessage());
        }
        $enable = (bool) $post->get('authentication_type_enabled');
        $wasEnabled = (bool) $authenticationType->getAuthenticationTypeStatus();
        if (!$this->error->has()) {
            $values = $post->all();
            unset($values['authentication_type_enabled']);
            if ($enable === true) {
                try {
                    $authenticationType->controller->saveAuthenticationType($values);
                } catch (Exception $x) {
                    $this->error->add($x->getMessage());
                }
            }
            if (!$this->error->has()) {
                try {
                    if ($enable && !$wasEnabled) {
                        $authenticationType->enable();
                    } elseif (!$enable && $wasEnabled) {
                        $authenticationType->disable();
                    }
                } catch (Exception $x) {
                    $this->error->add($x->getMessage());
                }
            }
        }
        if ($this->error->has()) {
            return $this->edit($authenticationType->getAuthenticationTypeID());
        }
        if ($enable === $wasEnabled) {
            $this->flash('success', t('The %s authentication type has been saved.', $authenticationType->getAuthenticationTypeDisplayName('text')));
        } elseif ($enable) {
            $this->flash('success', t('The %s authentication type has been enabled.', $authenticationType->getAuthenticationTypeDisplayName('text')));
        } else {
            $this->flash('success', t('The %s authentication type has been disabled.', $authenticationType->getAuthenticationTypeDisplayName('text')));
        }
        if ($this->error->has()) {
            return $this->edit($authenticationType->getAuthenticationTypeID());
        }

        return $this->buildRedirect($this->action());
    }
}
