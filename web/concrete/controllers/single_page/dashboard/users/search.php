<?php
namespace Concrete\Controller\SinglePage\Dashboard\Users;

use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
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
            if (is_uploaded_file($_FILES['avatar']['tmp_name'])) {
                $image = \Image::open($_FILES['avatar']['tmp_name']);
                $image = $image->thumbnail(
                    new Box(
                        Config::get('concrete.icons.user_avatar.width'),
                        Config::get('concrete.icons.user_avatar.height')
                    )
                );
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
            $this->set('canSignInAsUser', $this->canSignInAsUser);
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
                    if (Config::get('concrete.user.registration.notification_email')) {
                        $mh->from(
                            Config::get('concrete.user.registration.notification_email'),
                            t('Website Registration Notification')
                        );
                    } else {
                        $adminUser = UserInfo::getByID(USER_SUPER_ID);
                        $mh->from($adminUser->getUserEmail(), t('Website Registration Notification'));
                    }
                    $mh->addParameter('uID', $this->user->getUserID());
                    $mh->addParameter('user', $this->user);
                    $mh->addParameter('uName', $this->user->getUserName());
                    $mh->addParameter('uEmail', $this->user->getUserEmail());
                    $mh->addParameter('siteName', Config::get('concrete.site'));
                    $mh->load('user_registered_approval_complete');
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
            } elseif (!Loader::helper('concrete/validation')->isUniqueEmail($email) && $this->user->getUserEmail(
                ) != $email
            ) {
                $this->error->add(t("The email address '%s' is already in use. Please choose another.", $email));
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
            if (!Config::get('concrete.user.registration.email_registration')) {
                if (!Loader::helper('validation/token')->validate()) {
                    $this->error->add(Loader::helper('validation/token')->getErrorMessage());
                }
                if (strlen($username) < Config::get('concrete.user.username.minimum')) {
                    $this->error->add(
                        t(
                            'A username must be at least %s characters long.',
                            Config::get('concrete.user.username.minimum')
                        )
                    );
                }

                if (strlen($username) > Config::get('concrete.user.username.maximum')) {
                    $this->error->add(
                        t(
                            'A username cannot be more than %s characters long.',
                            Config::get('concrete.user.username.maximum')
                        )
                    );
                }

                if (strlen($username) >= Config::get('concrete.user.username.minimum') && !Loader::helper(
                        'concrete/validation'
                    )->username($username)
                ) {
                    if (Config::get('concrete.user.username.allow_spaces')) {
                        $this->error->add(
                            t(
                                'A username may only contain letters, numbers, spaces, dots (not at the beginning/end), underscores (not at the beginning/end).'
                            )
                        );
                    } else {
                        $this->error->add(
                            t(
                                'A username may only contain letters numbers, dots (not at the beginning/end), underscores (not at the beginning/end).'
                            )
                        );
                    }
                }
                $uo = $this->user->getUserObject();
                if (strcasecmp($uo->getUserName(), $username) && !Loader::Helper(
                        'concrete/validation'
                    )->isUniqueUsername($username)
                ) {
                    $this->error->add(t("The username '%s' already exists. Please choose another", $username));
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

                $this->user->saveUserAttributesForm(array($ak));
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
            $sr->setAdditionalDataAttribute('value', $val->getValue('displaySanitized', 'display'));
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
            if ((strlen($password) < Config::get('concrete.user.password.minimum')) || (strlen($password) > Config::get(
                        'concrete.user.password.maximum'
                    ))
            ) {
                $this->error->add(
                    t(
                        'A password must be between %s and %s characters',
                        Config::get('concrete.user.password.minimum'),
                        Config::get('concrete.user.password.maximum')
                    )
                );
            }
            if (!Loader::helper('validation/token')->validate('change_password')) {
                $this->error->add(Loader::helper('validation/token')->getErrorMessage());
            }
            if (strlen($password) >= Config::get('concrete.user.password.minimum') && !Loader::helper(
                    'concrete/validation'
                )->password($password)
            ) {
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
        if (array_key_exists('query', $_GET) && is_string($_GET['query'])) {
            $query = preg_replace('/\s+/', ' ', $_GET['query']);
        } else {
            $query = '';
        }
        $timezones = Loader::helper("date")->getTimezones();
        $result = array();
        foreach ($timezones as $timezoneID => $timezoneName) {
            if (($query === '') || (stripos($timezoneName, $query) !== false)) {
                $obj = new stdClass();
                $obj->id = $timezoneID;
                $obj->text = $timezoneName;
                $result[] = $obj;
            }
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
            $obj = new stdClass();
            $obj->value = $lang;
            $obj->text = \Punic\Language::getName($lang);
            $result[] = $obj;
        }
        usort(
            $result,
            function ($a, $b) {
                if ($a->value === '') {
                    $cmp = -1;
                } elseif ($b->value === '') {
                    $cmp = 1;
                } else {
                    $cmp = strcasecmp($a->text, $b->text);
                }

                return $cmp;
            }
        );
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

        $this->requireAsset('select2');

        $ui = $this->user;
        if (is_object($ui)) {
            $dh = Core::make('helper/date');
            /* @var $dh \Concrete\Core\Localization\Service\Date */
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
            $result = $cnt->getSearchResultObject();
            if (is_object($result)) {
                $object = $result->getJSONObject();
                $result = Loader::helper('json')->encode($object);
                $this->addFooterItem(
                    "<script type=\"text/javascript\">
                        $(function () {
                            $('div[data-search=users]').concreteAjaxSearch({
                                result: " . $result . ",
                                onLoad: function (concreteSearch) {
                                    concreteSearch.\$element.on('click', 'a[data-user-id]', function () {
                                        window.location.href='"
                                            . rtrim(URL::to('/dashboard/users/search', 'view'), '/')
                                            . "/' + $(this).attr('data-user-id');
                                        return false;
                                    });
                                }
                            });
                        });
                    </script>"
                );
            }
        }
    }

}
