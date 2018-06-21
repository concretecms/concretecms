<?php

namespace Concrete\Controller\SinglePage\Dashboard\Users;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\User\Validation\UsernameValidator;
use Config;
use Group;
use GroupList;
use Imagine\Image\Box;
use Loader;
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

        $this->set('form', Loader::helper('form'));
        $this->set('valt', Loader::helper('validation/token'));
        $this->set('valc', Loader::helper('concrete/validation'));
        $this->set('ih', Loader::helper('concrete/ui'));
        $this->set('av', Loader::helper('concrete/avatar'));
        $this->set('dtt', Loader::helper('form/date_time'));
        $this->set('gArray', $gArray);
        $this->set('assignment', $assignment);
        $this->set('locales', $locales);
        $this->set('attribs', $attribs);
    }

    public function submit()
    {
        $assignment = PermissionKey::getByHandle('edit_user_properties')->getMyAssignment();
        $vals = $this->app->make('helper/validation/strings');
        $valt = $this->app->make('helper/validation/token');
        $valc = $this->app->make('helper/concrete/validation');
        $usernameValidator = $this->app->make(UsernameValidator::class);

        $username = trim($_POST['uName']);
        $username = preg_replace("/\s+/", ' ', $username);
        $_POST['uName'] = $username;

        $password = $_POST['uPassword'];

        if (!$vals->email($_POST['uEmail'])) {
            $this->error->add(t('Invalid email address provided.'));
        } elseif (!$valc->isUniqueEmail($_POST['uEmail'])) {
            $this->error->add(t("The email address '%s' is already in use. Please choose another.", $_POST['uEmail']));
        }

        $this->error->add($usernameValidator->describeError($usernameValidator->check($username)));

        \Core::make('validator/password')->isValid($password, $this->error);

        if (!$valt->validate('submit')) {
            $this->error->add($valt->getErrorMessage());
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
                    if (is_uploaded_file($_FILES['uAvatar']['tmp_name'])) {
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
                if (is_array($_POST['gID'])) {
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
