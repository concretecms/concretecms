<?php

namespace Concrete\Controller\SinglePage\Dashboard\Users;

use Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Group;
use GroupList;
use Imagine\Image\Box;
use Localization;
use PermissionKey;
use Permissions;
use UserAttributeKey;
use UserInfo;

class Add extends DashboardPageController
{
    public function view()
    {
        $locales = Localization::getAvailableInterfaceLanguageDescriptions();
        $attribs = UserAttributeKey::getRegistrationList();
        $assignment = PermissionKey::getByHandle('edit_user_properties')->getMyAssignment();
        $gl = new GroupList();
        $gArray = $gl->getPagination()->setMaxPerPage(10000)->getCurrentPageResults();

        $this->set('form', $this->app->make('helper/form'));
        $this->set('valt', $this->app->make('helper/validation/token'));
        $this->set('valc', $this->app->make('helper/concrete/validation'));
        $this->set('ih', $this->app->make('helper/concrete/ui'));
        $this->set('av', $this->app->make('helper/concrete/avatar'));
        $this->set('dtt', $this->app->make('helper/form/date_time'));
        $this->set('gArray', $gArray);
        $this->set('assignment', $assignment);
        $this->set('locales', $locales);
        $this->set('attribs', $attribs);
    }

    public function submit()
    {
        $assignment = PermissionKey::getByHandle('edit_user_properties')->getMyAssignment();

        $username = trim($_POST['uName']);
        $username = preg_replace("/\s+/", ' ', $username);
        $_POST['uName'] = $username;

        $password = $_POST['uPassword'];

        $this->app->make('validator/user/name')->isValid($username, $this->error);

        $this->app->make('validator/password')->isValid($password, $this->error);

        $this->app->make('validator/user/email')->isValid($_POST['uEmail'], $this->error);

        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $aks = UserAttributeKey::getRegistrationList();

        foreach ($aks as $uak) {
            $controller = $uak->getController();
            $validator = $controller->getValidator();
            $response = $validator->validateSaveValueRequest(
                $controller, $this->request, $uak->isAttributeKeyRequiredOnRegister()
            );
            if (!$response->isValid()) {
                $error = $response->getErrorObject();
                $this->error->add($error);
            }
        }

        if (!$this->error->has()) {
            // do the registration
            $data = ['uName' => $username, 'uPassword' => $password, 'uEmail' => $_POST['uEmail'], 'uDefaultLanguage' => $_POST['uDefaultLanguage']];
            $uo = UserInfo::add($data);
            if (is_object($uo)) {
                if ($assignment->allowEditAvatar()) {
                    if (!empty($_FILES['uAvatar']) && is_uploaded_file($_FILES['uAvatar']['tmp_name'])) {
                        $image = \Image::open($_FILES['uAvatar']['tmp_name']);
                        $image = $image->thumbnail(new Box(
                                                       Config::get('concrete.icons.user_avatar.width'),
                                                       Config::get('concrete.icons.user_avatar.height')
                                                   ));
                        $uo->updateUserAvatar($image);
                    }
                }

                $saveAttributes = [];
                foreach ($aks as $uak) {
                    if (in_array($uak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) {
                        $saveAttributes[] = $uak;
                    }
                }

                if (count($saveAttributes) > 0) {
                    $uo->saveUserAttributesForm($saveAttributes);
                }

                $gIDs = [];
                if (!empty($_POST['gID']) && is_array($_POST['gID'])) {
                    foreach ($_POST['gID'] as $gID) {
                        $gx = Group::getByID($gID);
                        $gxp = new Permissions($gx);
                        if ($gxp->canAssignGroup()) {
                            $gIDs[] = $gID;
                        }
                    }
                }

                $uo->updateGroups($gIDs);
                $uID = $uo->getUserID();
                $this->redirect('/dashboard/users/search', 'view', $uID, 'created');
            } else {
                $this->error->add(t('An error occurred while trying to create the account.'));
                $this->set('error', $this->error);
            }
        } else {
            $this->view();
        }
    }
}
