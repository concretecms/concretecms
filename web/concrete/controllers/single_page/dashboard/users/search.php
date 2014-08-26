<?php
namespace Concrete\Controller\SinglePage\Dashboard\Users;

use \Concrete\Core\Page\Controller\DashboardPageController;
use Imagine\Image\Box;
use Loader;
use Exception;
use User;
use Core;
use UserInfo;
use URL;
use stdClass;
use Permissions;
use PermissionKey;
use UserAttributeKey;
use Localization;
use Zend_Locale;
use Zend_Locale_Data;
use Cache;
use \Concrete\Controller\Search\Users as SearchUsersController;
use \Concrete\Core\User\EditResponse as UserEditResponse;

class Search extends DashboardPageController
{
    protected $user = false;

    public function update_avatar($uID = false)
    {
        $this->setupUser($uID);
        if (!Loader::helper('validation/token')->validate()) {
            throw new Exception(Loader::helper('validation/token')->getErrorMessage());
        }
        if ($this->canEditAvatar) {
            $av = Loader::helper('concrete/avatar');
            if (is_uploaded_file($_FILES['avatar']['tmp_name']) ) {
                $image = \Image::open($_FILES['avatar']['tmp_name']);
                $image = $image->thumbnail(new Box(AVATAR_WIDTH, AVATAR_HEIGHT));
                $this->user->updateUserAvatar($image);
            } else {
                if ($_POST['task'] == 'clear') {
                    $this->user->update(array('uHasAvatar' => 0));
                }
            }
        } else {
            throw new Exception(t('Access Denied.'));
        }

        $ui = UserInfo::getByID($uID); // avatar doesn't reload automatically
        $sr = new UserEditResponse();
        $sr->setUser($this->user);
        $sr->setMessage(t('Avatar saved successfully.'));
        $html = $av->outputUserAvatar($ui);
        $sr->setAdditionalDataAttribute('imageHTML', $html);
        $sr->outputJSON();
    }

    protected function setupUser($uID)
    {
        $ui = UserInfo::getByID(Loader::helper('security')->sanitizeInt($uID));
        if (is_object($ui)) {
            $up = new Permissions($ui);
            if (!$up->canViewUser()) {
                throw new Exception(t('Access Denied.'));
            }
            $tp = new Permissions();
            $pke = PermissionKey::getByHandle('edit_user_properties');
            $this->user = $ui;
            $this->assignment = $pke->getMyAssignment();
            $this->canEdit = $up->canEditUser();
            if ($this->canEdit) {
                $this->canActivateUser = $tp->canActivateUser();
                $this->canEditAvatar = $this->assignment->allowEditAvatar();
                $this->canEditUserName = $this->assignment->allowEditUserName();
                $this->canEditLanguage = $this->assignment->allowEditDefaultLanguage();
                $this->canEditTimezone = $this->assignment->allowEditTimezone();
                $this->canEditEmail = $this->assignment->allowEditEmail();
                $this->canEditPassword = $this->assignment->allowEditPassword();
                $this->canSignInAsUser = $tp->canSudo();
                $this->canDeleteUser = $tp->canDeleteUser();
                $this->canAddGroup = $tp->canAccessGroupSearch();
                $this->allowedEditAttributes = $this->assignment->getAttributesAllowedArray();
            }
            $this->set('user', $ui);
            $this->set('canEditAvatar', $this->canEditAvatar);
            $this->set('canEditUserName', $this->canEditUserName);
            $this->set('canEditEmail', $this->canEditEmail);
            $this->set('canEditPassword', $this->canEditPassword);
            $this->set('canEditTimezone', $this->canEditTimezone);
            $this->set('canEditLanguage', $this->canEditLanguage);
            $this->set('canActivateUser', $this->canActivateUser);
            $this->set('canSignInAsUser', $this->canSignInAsUser );
            $this->set('canDeleteUser', $this->canDeleteUser);
            $this->set('allowedEditAttributes', $this->allowedEditAttributes);
            $this->set('canAddGroup', $this->canAddGroup);
        }
    }

    public function update_status($uID = false)
    {
        switch ($_POST['task']) {
            case 'activate':
                $this->setupUser($uID);
                if ($this->canActivateUser && Loader::helper('validation/token')->validate()) {
                    $this->user->activate();
                    $mh = Loader::helper('mail');
                    $mh->to($this->user->getUserEmail());
                    $mh->load('user_registered_approval_complete');
                    if (defined('EMAIL_ADDRESS_REGISTER_NOTIFICATION_FROM')) {
                        $mh->from(EMAIL_ADDRESS_REGISTER_NOTIFICATION_FROM, t('Website Registration Notification'));
                    } else {
                        $adminUser = UserInfo::getByID(USER_SUPER_ID);
                        $mh->from($adminUser->getUserEmail(), t('Website Registration Notification'));
                    }
                    $mh->addParameter('uID',    $this->user->getUserID());
                    $mh->addParameter('user',   $this->user);
                    $mh->addParameter('uName',  $this->user->getUserName());
                    $mh->addParameter('uEmail', $this->user->getUserEmail());
                    $mh->sendMail();
                    $this->redirect('/dashboard/users/search', 'view', $this->user->getUserID(), 'activated');
                }
                break;
            case 'deactivate':
                $this->setupUser($uID);
                if ($this->canActivateUser && Loader::helper('validation/token')->validate()) {
                    $this->user->deactivate();
                    $this->redirect('/dashboard/users/search', 'view', $this->user->getUserID(), 'deactivated');
                }
                break;
            case 'validate':
                $this->setupUser($uID);
                if ($this->canActivateUser && Loader::helper('validation/token')->validate()) {
                    $this->user->markValidated();
                    $this->redirect('/dashboard/users/search', 'view', $this->user->getUserID(), 'email_validated');
                }
                break;
            case 'sudo':
                $this->setupUser($uID);
                if ($this->canSignInAsUser && Loader::helper('validation/token')->validate()) {
                    User::loginByUserID($uID);
                    $this->redirect('/');
                }
                break;
            case 'delete':
                $this->setupUser($uID);
                if ($this->canDeleteUser && Loader::helper('validation/token')->validate()) {
                    $this->user->delete();
                    $this->redirect('/dashboard/users/search', 'delete_complete');
                }
                break;
        }
        $this->view($uID);
    }

    public function update_email($uID = false)
    {
        $this->setupUser($uID);
        if ($this->canEditEmail) {
            $email = $this->post('value');
            if (!Loader::helper('validation/token')->validate()) {
                $this->error->add(Loader::helper('validation/token')->getErrorMessage());
            }
            if (!Loader::helper('validation/strings')->email($email)) {
                $this->error->add(t('Invalid email address provided.'));
            } elseif (!Loader::helper('concrete/validation')->isUniqueEmail($email) && $this->user->getUserEmail() != $email) {
                $this->error->add(t("The email address '%s' is already in use. Please choose another.",$email));
            }

            $sr = new UserEditResponse();
            $sr->setUser($this->user);
            if (!$this->error->has()) {
                $data = array('uEmail' => $email);
                $this->user->update($data);
                $sr->setMessage(t('Email saved successfully.'));
            } else {
                $sr->setError($this->error);
            }
            $sr->outputJSON();
        }
    }

    public function update_timezone($uID = false)
    {
        $this->setupUser($uID);
        if ($this->canEditTimezone) {
            $timezone = $this->post('value');
            if (!Loader::helper('validation/token')->validate()) {
                $this->error->add(Loader::helper('validation/token')->getErrorMessage());
            }
            $sr = new UserEditResponse();
            $sr->setUser($this->user);
            if (!$this->error->has()) {
                $data = array('uTimezone' => $timezone);
                $this->user->update($data);
                $sr->setMessage(t('Time zone saved successfully.'));
            } else {
                $sr->setError($this->error);
            }
            $sr->outputJSON();
        }
    }

    public function update_language($uID = false)
    {
        $this->setupUser($uID);
        if ($this->canEditLanguage) {
            $language = $this->post('value');
            if (!Loader::helper('validation/token')->validate()) {
                $this->error->add(Loader::helper('validation/token')->getErrorMessage());
            }
            $sr = new UserEditResponse();
            $sr->setUser($this->user);
            if (!$this->error->has()) {
                $data = array('uDefaultLanguage' => $language);
                $this->user->update($data);
                $sr->setMessage(t('Language saved successfully.'));
            } else {
                $sr->setError($this->error);
            }
            $sr->outputJSON();
        }
    }

    public function update_username($uID = false)
    {
        $this->setupUser($uID);
        if ($this->canEditUserName) {
            $username = $this->post('value');
            if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == false) {
                if (!Loader::helper('validation/token')->validate()) {
                    $this->error->add(Loader::helper('validation/token')->getErrorMessage());
                }
                if (strlen($username) < USER_USERNAME_MINIMUM) {
                    $this->error->add(t('A username must be at least %s characters long.',USER_USERNAME_MINIMUM));
                }

                if (strlen($username) > USER_USERNAME_MAXIMUM) {
                    $this->error->add(t('A username cannot be more than %s characters long.',USER_USERNAME_MAXIMUM));
                }


                if (strlen($username) >= USER_USERNAME_MINIMUM && !Loader::helper('concrete/validation')->username($username)) {
                    if (USER_USERNAME_ALLOW_SPACES) {
                        $this->error->add(t('A username may only contain letters, numbers, spaces, dots (not at the beginning/end), underscores (not at the beginning/end).'));
                    } else {
                        $this->error->add(t('A username may only contain letters numbers, dots (not at the beginning/end), underscores (not at the beginning/end).'));
                    }
                }
                $uo = $this->user->getUserObject();
                if (strcasecmp($uo->getUserName(), $username) && !Loader::Helper('concrete/validation')->isUniqueUsername($username)) {
                    $this->error->add(t("The username '%s' already exists. Please choose another",$username));
                }

                $sr = new UserEditResponse();
                $sr->setUser($this->user);
                if (!$this->error->has()) {
                    $data = array('uName' => $username);
                    $this->user->update($data);
                    $sr->setMessage(t('Username saved successfully.'));
                } else {
                    $sr->setError($this->error);
                }
                $sr->outputJSON();
            }
        }
    }

    public function update_attribute($uID = false)
    {
        $this->setupUser($uID);
        $sr = new UserEditResponse();
        if (Loader::helper('validation/token')->validate()) {
            $ak = UserAttributeKey::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['name']));
            if (is_object($ak)) {
                if (!in_array($ak->getAttributeKeyID(), $this->allowedEditAttributes)) {
                    throw new Exception(t('You do not have permission to modify this attribute.'));
                }

                $ak->saveAttributeForm($this->user);
                $val = $this->user->getAttributeValueObject($ak);
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        $sr->setUser($this->user);
        if ($this->error->has()) {
            $sr->setError($this->error);
        } else {
            $sr->setMessage(t('Attribute saved successfully.'));
            $sr->setAdditionalDataAttribute('value',  $val->getValue('displaySanitized','display'));
        }
        $this->user->reindex();
        $sr->outputJSON();
    }

    public function clear_attribute($uID = false)
    {
        $this->setupUser($uID);
        $sr = new UserEditResponse();
        if (Loader::helper('validation/token')->validate()) {
            $ak = UserAttributeKey::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['akID']));
            if (is_object($ak)) {
                if (!in_array($ak->getAttributeKeyID(), $this->allowedEditAttributes)) {
                    throw new Exception(t('You do not have permission to modify this attribute.'));
                }
                $this->user->clearAttribute($ak);
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        $sr->setUser($this->user);
        if ($this->error->has()) {
            $sr->setError($this->error);
        } else {
            $sr->setMessage(t('Attribute cleared successfully.'));
        }
        $sr->outputJSON();
    }

    public function change_password($uID = false)
    {
        $this->setupUser($uID);
        if ($this->canEditPassword) {
            $password = $this->post('uPassword');
            $passwordConfirm = $this->post('uPasswordConfirm');
            if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
                $this->error->add( t('A password must be between %s and %s characters',USER_PASSWORD_MINIMUM,USER_PASSWORD_MAXIMUM));
            }
            if (!Loader::helper('validation/token')->validate('change_password')) {
                $this->error->add(Loader::helper('validation/token')->getErrorMessage());
            }
            if (strlen($password) >= USER_PASSWORD_MINIMUM && !Loader::helper('concrete/validation')->password($password)) {
                $this->error->add(t('A password may not contain ", \', >, <, or any spaces.'));
            }

            if ($password != $passwordConfirm) {
                $this->error->add(t('The two passwords provided do not match.'));
            }

            $sr = new UserEditResponse();
            $sr->setUser($this->user);
            if (!$this->error->has()) {
                $data['uPassword'] = $password;
                $data['uPasswordConfirm'] = $passwordConfirm;
                $this->user->update($data);
                $sr->setMessage(t('Password updated successfully.'));
            } else {
                $sr->setError($this->error);
            }
            $sr->outputJSON();
        }
    }

    public function get_timezones()
    {
        $timezones = Loader::helper("date")->getTimezones();
        $result = array();
        foreach ($timezones as $timezoneID => $timezoneName) {
            $obj = new stdClass();
            $obj->value = $timezoneID;
            $obj->text = $timezoneName;
            $result[] = $obj;
        }
        Loader::helper('ajax')->sendResult($result);
    }

    public function get_languages()
    {
        $languages = Localization::getAvailableInterfaceLanguages();
        array_unshift($languages, 'en_US');
        $obj = new stdClass();
        $obj->text = tc('Default locale', '** Default');
        $obj->value = '';
        $result = array($obj);
        foreach ($languages as $lang) {
            $loc = new Zend_Locale($lang);
            $obj = new stdClass();
            $obj->value = $lang;
            $obj->text = Zend_Locale::getTranslation($loc->getLanguage(), 'language', $lang);
            $result[] = $obj;
        }
        Loader::helper('ajax')->sendResult($result);

    }

    public function delete_complete()
    {
        $this->set('message', t('User deleted successfully.'));
        $this->view();
    }

    public function view($uID = false, $status = false)
    {
        if ($uID) {
            $this->setupUser($uID);
        }

        $ui = $this->user;
        if (is_object($ui)) {
            $dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
            $this->requireAsset('core/app/editable-fields');
            $uo = $this->user->getUserObject();
            $groups = array();
            foreach ($uo->getUserGroupObjects() as $g) {
                $obj = new stdClass();
                $obj->gDisplayName = $g->getGroupDisplayName();
                $obj->gID = $g->getGroupID();
                $obj->gDateTimeEntered = $dh->formatDateTime($g->getGroupDateTimeEntered($this->user));
                $groups[] = $obj;
            }
            $this->set('groupsJSON', Loader::helper('json')->encode($groups));
            $attributes = UserAttributeKey::getList(true);
            $this->set('attributes', $attributes);
            $this->set('pageTitle', t('View/Edit %s', $this->user->getUserDisplayName()));
            switch ($status) {
                case 'activated':
                    $this->set('success', t('User activated successfully.'));
                    break;
                case 'deactivated':
                    $this->set('message', t('User deactivated successfully.'));
                    break;
                case 'created':
                    $this->set('message', t('User created successfully.'));
                    break;
                case 'email_validated':
                    $this->set('message', t('Email marked as valid.'));
                    break;
            }
        } else {

            $cnt = new SearchUsersController();
            $cnt->search();
            $this->set('searchController', $cnt);
            $object = $cnt->getSearchResultObject()->getJSONObject();
            $result = Loader::helper('json')->encode($object);
            $this->addFooterItem("<script type=\"text/javascript\">
			$(function () {
				$('div[data-search=users]').concreteAjaxSearch({
					result: " . $result . ",
					onLoad: function (concreteSearch) {
						concreteSearch.\$element.on('click', 'a[data-user-id]', function () {
							window.location.href='" . URL::to('/dashboard/users/search', 'view') . "/' + $(this).attr('data-user-id');
							return false;
						});
					}
				});
			});
			</script>");
        }
    }

}
