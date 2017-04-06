<?php
namespace Concrete\Controller\SinglePage\Dashboard\Users;

use Concrete\Controller\Element\Search\Users\Header;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\User\CsvWriter;
use Concrete\Core\User\EditResponse as UserEditResponse;
use Concrete\Core\Workflow\Progress\UserProgress as UserWorkflowProgress;
use Imagine\Image\Box;
use Exception;
use Core;
use League\Csv\Writer;
use Permissions;
use PermissionKey;
use stdClass;
use Symfony\Component\HttpFoundation\StreamedResponse;
use User;
use UserAttributeKey;
use UserInfo;

class Search extends DashboardPageController
{
    protected $user = false;

    public function update_avatar($uID = false)
    {
        $this->setupUser($uID);
        if (!$this->app->make('helper/validation/token')->validate()) {
            throw new Exception($this->app->make('helper/validation/token')->getErrorMessage());
        }
        if ($this->canEditAvatar) {
            $file = $this->request->files->get('avatar');
            if ($file !== null) {
                /* @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
                if (!$file->isValid()) {
                    throw new Exception($file->getErrorMessage());
                }
                $image = \Image::open($file->getPathname());
                $config = $this->app->make('config');
                $image = $image->thumbnail(
                    new Box(
                        $config->get('concrete.icons.user_avatar.width'),
                        $config->get('concrete.icons.user_avatar.height')
                    )
                );
                $this->user->updateUserAvatar($image);
            } elseif ($this->request->post('task') == 'clear') {
                $this->user->update(['uHasAvatar' => 0]);
            }
        } else {
            throw new Exception(t('Access Denied.'));
        }

        $ui = UserInfo::getByID($uID); // avatar doesn't reload automatically
        $sr = new UserEditResponse();
        $sr->setUser($this->user);
        $sr->setMessage(t('Avatar saved successfully.'));
        $av = $this->user->getUserAvatar();
        $html = $av->output();
        $sr->setAdditionalDataAttribute('imageHTML', $html);
        $sr->outputJSON();
    }

    protected function setupUser($uID)
    {
        $me = new User();
        $ui = UserInfo::getByID($this->app->make('helper/security')->sanitizeInt($uID));
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
                $this->canActivateUser = $tp->canActivateUser() && $me->getUserID() != $ui->getUserID();
                $this->canEditAvatar = $this->assignment->allowEditAvatar();
                $this->canEditUserName = $this->assignment->allowEditUserName();
                $this->canEditLanguage = $this->assignment->allowEditDefaultLanguage();
                $this->canEditTimezone = $this->assignment->allowEditTimezone();
                $this->canEditEmail = $this->assignment->allowEditEmail();
                $this->canEditPassword = $this->assignment->allowEditPassword();
                $this->canSignInAsUser = $tp->canSudo() && $me->getUserID() != $ui->getUserID();
                $this->canDeleteUser = $tp->canDeleteUser() && $me->getUserID() != $ui->getUserID();
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
        switch ($this->request->post('task')) {
            case 'activate':
                $this->setupUser($uID);
                if ($this->canActivateUser && $this->app->make('helper/validation/token')->validate()) {
                    if ($this->user->triggerActivate()) {
                        $mh = $this->app->make('helper/mail');
                        $mh->to($this->user->getUserEmail());
                        $config = $this->app->make('config');
                        if ($config->get('concrete.user.registration.notification_email')) {
                            $mh->from(
                                $config->get('concrete.user.registration.notification_email'),
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
                        $mh->addParameter('siteName', $this->app->make('site')->getSite()->getSiteName());
                        $mh->load('user_registered_approval_complete');
                        $mh->sendMail();
                    }

                    $this->redirect('/dashboard/users/search', 'view', $this->user->getUserID(), 'activated');
                }
                break;
            case 'deactivate':
                $this->setupUser($uID);
                if ($this->canActivateUser && $this->app->make('helper/validation/token')->validate()) {
                    $this->user->triggerDeactivate();
                    $this->redirect('/dashboard/users/search', 'view', $this->user->getUserID(), 'deactivated');
                }
                break;
            case 'validate':
                $this->setupUser($uID);
                if ($this->canActivateUser && $this->app->make('helper/validation/token')->validate()) {
                    $this->user->markValidated();
                    $this->redirect('/dashboard/users/search', 'view', $this->user->getUserID(), 'email_validated');
                }
                break;
            case 'send_email_validation':
                $this->setupUser($uID);
                if ($this->canActivateUser && $this->app->make('helper/validation/token')->validate()) {
                    $this->app->make('user/status')->sendEmailValidation($this->user);
                    $this->redirect('/dashboard/users/search', 'view', $this->user->getUserID(), 'email_validation_sent');
                }
                break;
            case 'sudo':
                $this->setupUser($uID);
                if ($this->canSignInAsUser && $this->app->make('helper/validation/token')->validate()) {
                    User::loginByUserID($uID);
                    $this->redirect('/');
                }
                break;
            case 'delete':
                $this->setupUser($uID);
                if ($this->canDeleteUser && $this->app->make('helper/validation/token')->validate()) {
                    $this->user->triggerDelete($this->user);
                    $this->redirect('/dashboard/users/search', 'view', $this->user->getUserID(), 'deleted');
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
            if (!$this->app->make('helper/validation/token')->validate()) {
                $this->error->add($this->app->make('helper/validation/token')->getErrorMessage());
            }
            if (!$this->app->make('helper/validation/strings')->email($email)) {
                $this->error->add(t('Invalid email address provided.'));
            } elseif (!$this->app->make('helper/concrete/validation')->isUniqueEmail($email) && $this->user->getUserEmail(
                ) != $email
            ) {
                $this->error->add(t("The email address '%s' is already in use. Please choose another.", $email));
            }

            $sr = new UserEditResponse();
            $sr->setUser($this->user);
            if (!$this->error->has()) {
                $data = ['uEmail' => $email];
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
            if (!$this->app->make('helper/validation/token')->validate()) {
                $this->error->add($this->app->make('helper/validation/token')->getErrorMessage());
            }
            $sr = new UserEditResponse();
            $sr->setUser($this->user);
            if (!$this->error->has()) {
                $data = ['uTimezone' => $timezone];
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
            if (!$this->app->make('helper/validation/token')->validate()) {
                $this->error->add($this->app->make('helper/validation/token')->getErrorMessage());
            }
            $sr = new UserEditResponse();
            $sr->setUser($this->user);
            if (!$this->error->has()) {
                $data = ['uDefaultLanguage' => $language];
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
            $config = $this->app->make('config');
            $username = $this->post('value');
            if (!$this->app->make('helper/validation/token')->validate()) {
                $this->error->add($this->app->make('helper/validation/token')->getErrorMessage());
            }
            if (strlen($username) < $config->get('concrete.user.username.minimum')) {
                $this->error->add(
                    t(
                        'A username must be at least %s characters long.',
                        $config->get('concrete.user.username.minimum')
                    )
                );
            }

            if (strlen($username) > $config->get('concrete.user.username.maximum')) {
                $this->error->add(
                    t(
                        'A username cannot be more than %s characters long.',
                        $config->get('concrete.user.username.maximum')
                    )
                );
            }

            if (strlen($username) >= $config->get('concrete.user.username.minimum') && !$this->app->make('helper/concrete/validation')->username($username)) {
                if ($config->get('concrete.user.username.allow_spaces')) {
                    $this->error->add(
                        t(
                            'A username may only contain letters, numbers, spaces, dots (not at the beginning/end), underscores (not at the beginning/end).'
                        )
                    );
                } else {
                    $this->error->add(
                        t(
                            'A username may only contain letters, numbers, dots (not at the beginning/end), underscores (not at the beginning/end).'
                        )
                    );
                }
            }
            $uo = $this->user->getUserObject();
            if (strcasecmp($uo->getUserName(), $username) && !$this->app->make('helper/concrete/validation')->isUniqueUsername($username)) {
                $this->error->add(t("The username '%s' already exists. Please choose another", $username));
            }

            $sr = new UserEditResponse();
            $sr->setUser($this->user);
            if (!$this->error->has()) {
                $data = ['uName' => $username];
                $this->user->update($data);
                $sr->setMessage(t('Username saved successfully.'));
            } else {
                $sr->setError($this->error);
            }
            $sr->outputJSON();
        }
    }

    public function update_attribute($uID = false)
    {
        $this->setupUser($uID);
        $sr = new UserEditResponse();
        if ($this->app->make('helper/validation/token')->validate()) {
            $ak = UserAttributeKey::getByID($this->app->make('helper/security')->sanitizeInt($this->request->request('name')));
            if (is_object($ak)) {
                if (!in_array($ak->getAttributeKeyID(), $this->allowedEditAttributes)) {
                    throw new Exception(t('You do not have permission to modify this attribute.'));
                }

                $this->user->saveUserAttributesForm([$ak]);
                $val = $this->user->getAttributeValueObject($ak);
            }
        } else {
            $this->error->add($this->app->make('helper/validation/token')->getErrorMessage());
        }
        $sr->setUser($this->user);
        if ($this->error->has()) {
            $sr->setError($this->error);
        } else {
            $sr->setMessage(t('Attribute saved successfully.'));
            $sr->setAdditionalDataAttribute('value', $val->getDisplayValue());
        }
        $this->user->reindex();
        $sr->outputJSON();
    }

    public function clear_attribute($uID = false)
    {
        $this->setupUser($uID);
        $sr = new UserEditResponse();
        if ($this->app->make('helper/validation/token')->validate()) {
            $ak = UserAttributeKey::getByID($this->app->make('helper/security')->sanitizeInt($this->request->request('akID')));
            if (is_object($ak)) {
                if (!in_array($ak->getAttributeKeyID(), $this->allowedEditAttributes)) {
                    throw new Exception(t('You do not have permission to modify this attribute.'));
                }
                $this->user->clearAttribute($ak);
            }
        } else {
            $this->error->add($this->app->make('helper/validation/token')->getErrorMessage());
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

            $this->app->make('validator/password')->isValid($password, $this->error);

            if (!$this->app->make('helper/validation/token')->validate('change_password')) {
                $this->error->add($this->app->make('helper/validation/token')->getErrorMessage());
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
        $query = $this->request->get('query');
        if (is_string($query)) {
            $query = preg_replace('/\s+/', ' ', $query);
        } else {
            $query = '';
        }
        $timezones = $this->app->make('helper/date')->getTimezones();
        $result = [];
        foreach ($timezones as $timezoneID => $timezoneName) {
            if (($query === '') || (stripos($timezoneName, $query) !== false)) {
                $obj = new stdClass();
                $obj->value = $timezoneID;
                $obj->text = $timezoneName;
                $result[] = $obj;
            }
        }
        $this->app->make('helper/ajax')->sendResult($result);
    }

    public function get_languages()
    {
        $languages = Localization::getAvailableInterfaceLanguages();
        array_unshift($languages, Localization::BASE_LOCALE);
        $obj = new stdClass();
        $obj->text = tc('Default locale', '** Default');
        $obj->value = '';
        $result = [$obj];
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
        $this->app->make('helper/ajax')->sendResult($result);
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

        $this->requireAsset('selectize');

        $ui = $this->user;
        if (is_object($ui)) {
            $dh = $this->app->make('helper/date');
            /* @var $dh \Concrete\Core\Localization\Service\Date */
            $this->requireAsset('core/app/editable-fields');
            $uo = $this->user->getUserObject();
            $groups = [];
            foreach ($uo->getUserGroupObjects() as $g) {
                $obj = new stdClass();
                $obj->gDisplayName = $g->getGroupDisplayName();
                $obj->gID = $g->getGroupID();
                $obj->gDateTimeEntered = $dh->formatDateTime($g->getGroupDateTimeEntered($this->user));
                $groups[] = $obj;
            }
            $this->set('groupsJSON', json_encode($groups));

            $service = $this->app->make(CategoryService::class);
            $categoryEntity = $service->getByHandle('user');
            $category = $categoryEntity->getController();
            $setManager = $category->getSetManager();
            $sets = $setManager->getAttributeSets();
            $unassigned = $setManager->getUnassignedAttributeKeys();
            $this->set('attributeSets', $sets);
            $this->set('unassigned', $unassigned);

            $this->set('pageTitle', t('View/Edit %s', $this->user->getUserDisplayName()));

            $workflowRequestActions = [];
            $workflowList = UserWorkflowProgress::getList($uo->getUserID());

            $this->set('workflowList', $workflowList);

            if (count($workflowList) > 0) {
                foreach ($workflowList as $wp) {
                    $wr = $wp->getWorkflowRequestObject();
                    $workflowRequestActions[] = $wr->getRequestAction();
                }
            }

            $this->set('workflowRequestActions', $workflowRequestActions);
            $headerMenu = new \Concrete\Controller\Element\Dashboard\Users\Header($this->user);
            $headerMenu->set('canActivateUser', $this->canActivateUser);
            $headerMenu->set('canSignInAsUser', $this->canSignInAsUser);
            $headerMenu->set('canDeleteUser', $this->canDeleteUser);
            $headerMenu->set('workflowRequestActions', $workflowRequestActions);
            $this->set('headerMenu', $headerMenu);

            switch ($status) {
                case 'activated':
                    if (in_array("activate", $workflowRequestActions)) {
                        $this->set('message', t('User activation workflow initiated.'));
                    } else {
                        $this->set('success', t('User activated successfully.'));
                    }
                    break;
                case 'deactivated':
                    if (in_array("deactivate", $workflowRequestActions)) {
                        $this->set('message', t('User deactivation workflow initiated.'));
                    } else {
                        $this->set('message', t('User deactivated successfully.'));
                    }
                    break;
                case 'created':
                    $this->set('message', t('User created successfully.'));
                    break;
                case 'email_validated':
                    $this->set('message', t('Email marked as valid.'));
                    break;
                case 'email_validation_sent':
                    $this->set('message', t('Email validation sent.'));
                    break;
                case 'workflow_canceled':
                    $this->set('message', t('Workflow request is canceled.'));
                    break;
                case 'deleted':
                    // TODO show username
                    // $this->set('message', t('User %s has been deleted.', $ui->getUserDisplayName()));
                    if (in_array('delete', $workflowRequestActions)) {
                        $this->set('message', t('User deletion workflow initiated.'));
                    } else {
                        $this->set('message', t('User has been deleted.'));
                    }
                    break;
            }
        } else {
            switch ($status) {
                case 'deleted':
                    $this->set('message', t('User has been deleted.'));
                    break;
            }

            $header = new Header();
            $header->setShowAddButton(true);
            $this->set('headerMenu', $header);

            $search = $this->app->make('Concrete\Controller\Search\Users');
            $result = $search->getCurrentSearchObject();

            if (is_object($result)) {
                $this->set('result', $result);
            }
        }
    }

    /**
     * Export Users using the current search filters into a CSV.
     */
    public function csv_export()
    {
        $search = $this->app->make('Concrete\Controller\Search\Users');
        $result = $search->getCurrentSearchObject();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=concrete5_users.csv'
        ];
        $app = $this->app;

        return StreamedResponse::create(
            function() use ($app, $result) {
                $writer = $app->make(CsvWriter::class, [
                    Writer::createFromPath('php://output', 'w')
                ]);

                $writer->insertHeaders();
                $writer->insertUserList($result->getItemListObject());
            },
            200,
            $headers);
    }
}
