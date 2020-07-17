<?php

namespace Concrete\Controller\SinglePage\Dashboard\Users;

use Concrete\Controller\Element\Search\Users\Header;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Csv\Export\UserExporter;
use Concrete\Core\Csv\WriterFactory;
use Concrete\Core\Entity\Search\SavedUserSearch;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Navigation\Breadcrumb\Dashboard\DashboardUserBreadcrumbFactory;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\User\EditResponse as UserEditResponse;
use Concrete\Core\User\User;
use Concrete\Core\Workflow\Progress\UserProgress as UserWorkflowProgress;
use Exception;
use Imagine\Image\Box;
use PermissionKey;
use Permissions;
use stdClass;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UserAttributeKey;
use UserInfo;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\User\Search\Menu\MenuFactory;
use Concrete\Core\User\Search\SearchProvider;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Search\Result\ResultFactory;
use Symfony\Component\HttpFoundation\Request;

class Search extends DashboardPageController
{
    /**
     * @var \Concrete\Core\User\UserInfo|false
     */
    protected $user = false;
    /**
     * @var Element
     */
    protected $headerMenu;

    /**
     * @var Element
     */
    protected $headerSearch;

    /**
     * @return SearchProvider
     */
    protected function getSearchProvider()
    {
        return $this->app->make(SearchProvider::class);
    }

    /**
     * @return QueryFactory
     */
    protected function getQueryFactory()
    {
        return $this->app->make(QueryFactory::class);
    }

    protected function getHeaderMenu()
    {
        if (!isset($this->headerMenu)) {
            $this->headerMenu = $this->app->make(ElementManager::class)->get('users/search/menu');
        }

        return $this->headerMenu;
    }

    protected function getHeaderSearch()
    {
        if (!isset($this->headerSearch)) {
            $this->headerSearch = $this->app->make(ElementManager::class)->get('users/search/search');
        }

        return $this->headerSearch;
    }

    /**
     * @param Result $result
     */
    protected function renderSearchResult(Result $result)
    {
        $headerMenu = $this->getHeaderMenu();
        $headerSearch = $this->getHeaderSearch();
        $headerMenu->getElementController()->setQuery($result->getQuery());
        $headerSearch->getElementController()->setQuery($result->getQuery());

        $this->set('resultsBulkMenu', $this->app->make(MenuFactory::class)->createBulkMenu());
        $this->set('result', $result);
        $this->set('headerMenu', $headerMenu);
        $this->set('headerSearch', $headerSearch);

        $this->setThemeViewTemplate('full.php');
    }

    /**
     * @param Query $query
     * @return Result
     */
    protected function createSearchResult(Query $query)
    {
        $provider = $this->app->make(SearchProvider::class);
        $resultFactory = $this->app->make(ResultFactory::class);
        $queryModifier = $this->app->make(QueryModifier::class);

        $queryModifier->addModifier(new AutoSortColumnRequestModifier($provider, $this->request, Request::METHOD_GET));
        $queryModifier->addModifier(new ItemsPerPageRequestModifier($provider, $this->request, Request::METHOD_GET));
        $query = $queryModifier->process($query);

        return $resultFactory->createFromQuery($provider, $query);
    }

    protected function getSearchKeywordsField()
    {
        $keywords = null;

        if ($this->request->query->has('keywords')) {
            $keywords = $this->request->query->get('keywords');
        }

        return new KeywordsField($keywords);
    }

    public function advanced_search()
    {
        $query = $this->getQueryFactory()->createFromAdvancedSearchRequest(
            $this->getSearchProvider(), $this->request, Request::METHOD_GET
        );

        $result = $this->createSearchResult($query);

        $this->renderSearchResult($result);
    }

    public function preset($presetID = null)
    {
        if ($presetID) {
            $preset = $this->entityManager->find(SavedUserSearch::class, $presetID);

            if ($preset) {
                /** @noinspection PhpParamsInspection */
                $query = $this->getQueryFactory()->createFromSavedSearch($preset);
                $result = $this->createSearchResult($query);
                $this->renderSearchResult($result);

                return;
            }
        }

        $this->view();
    }

    /**
     * @return DashboardUserBreadcrumbFactory
     */
    protected function createBreadcrumbFactory()
    {
        return $this->app->make(DashboardUserBreadcrumbFactory::class);
    }

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
                        if ($config->get('concrete.email.register_notification.address')) {
                            $mh->from($config->get('concrete.email.register_notification.address'), t('Website Registration Notification'));
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

                    $this->redirect('/dashboard/users/search', 'edit', $this->user->getUserID(), 'activated');
                }
                break;
            case 'deactivate':
                $this->setupUser($uID);
                if ($this->canActivateUser && $this->app->make('helper/validation/token')->validate()) {
                    $this->user->triggerDeactivate();
                    $this->redirect('/dashboard/users/search', 'edit', $this->user->getUserID(), 'deactivated');
                }
                break;
            case 'validate':
                $this->setupUser($uID);
                if ($this->canActivateUser && $this->app->make('helper/validation/token')->validate()) {
                    $this->user->markValidated();
                    $this->redirect('/dashboard/users/search', 'edit', $this->user->getUserID(), 'email_validated');
                }
                break;
            case 'send_email_validation':
                $this->setupUser($uID);
                if ($this->canActivateUser && $this->app->make('helper/validation/token')->validate()) {
                    $this->app->make('user/status')->sendEmailValidation($this->user);
                    $this->redirect('/dashboard/users/search', 'edit', $this->user->getUserID(), 'email_validation_sent');
                }
                break;
            case 'sudo':
                $this->setupUser($uID);
                if ($this->canSignInAsUser && $this->app->make('helper/validation/token')->validate()) {
                    $logger = $this->app->make(LoggerFactory::class)
                        ->createLogger(Channels::CHANNEL_USERS);
                    $me = $this->app->make(User::class);
                    $signInUser = UserInfo::getByID($uID);
                    $logger->notice(t('User %s used the dashboard to sign in as user %s',
                        $me->getUserName(), $signInUser->getUserName()));
                    User::loginByUserID($uID);
                    $this->redirect('/');
                }
                break;
            case 'delete':
                $this->setupUser($uID);
                if ($this->canDeleteUser && $this->app->make('helper/validation/token')->validate()) {
                    $this->user->triggerDelete($this->user);
                    $this->redirect('/dashboard/users/search', 'edit', $this->user->getUserID(), 'deleted');
                }
                break;
        }
        $this->view($uID);
    }

    public function update_email($uID = false)
    {
        $this->setupUser($uID);
        if ($this->user && $this->canEditEmail) {
            $email = $this->post('value');
            if (!$this->app->make('helper/validation/token')->validate()) {
                $this->error->add($this->app->make('helper/validation/token')->getErrorMessage());
            }
            $this->app->make('validator/user/email')->isValidFor($email, $this->user, $this->error);

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
        if ($this->user && $this->canEditUserName) {
            $username = $this->post('value');
            if (!$this->app->make('helper/validation/token')->validate()) {
                $this->error->add($this->app->make('helper/validation/token')->getErrorMessage());
            }
            $this->app->make('validator/user/name')->isValidFor($username, $this->user, $this->error);

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

            $this->app->make('validator/password')->isValidFor($password, $this->user, $this->error);

            if (!$this->app->make('helper/validation/token')->validate('change_password')) {
                $this->error->add($this->app->make('helper/validation/token')->getErrorMessage());
            }
            if ($password != $passwordConfirm) {
                $this->error->add(t('The two passwords provided do not match.'));
            }

            $sr = new UserEditResponse();
            $sr->setUser($this->user);
            if (!$this->error->has()) {
                $data = [
                    'uPassword' => $password,
                    'uPasswordConfirm' => $passwordConfirm,
                ];
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

    public function edit($uID = false, $status = false)
    {
        if ($uID) {
            $this->setupUser($uID);
        }

        $ui = $this->user;
        if (is_object($ui)) {
            $dh = $this->app->make('helper/date');
            /* @var $dh \Concrete\Core\Localization\Service\Date */
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
                    if (in_array('activate', $workflowRequestActions)) {
                        $this->set('message', t('User activation workflow initiated.'));
                    } else {
                        $this->set('success', t('User activated successfully.'));
                    }
                    break;
                case 'deactivated':
                    if (in_array('deactivate', $workflowRequestActions)) {
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

        $factory = $this->createBreadcrumbFactory();
        $this->setBreadcrumb($factory->getBreadcrumb($this->getPageObject(), $ui));

        $this->render("/dashboard/users/search/edit");
    }

    public function view() {
        $query = $this->getQueryFactory()->createQuery($this->getSearchProvider(), [
            $this->getSearchKeywordsField()
        ]);

        $result = $this->createSearchResult($query);

        $this->renderSearchResult($result);

        $this->headerSearch->getElementController()->setQuery(null);
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
            'Content-Disposition' => 'attachment; filename=concrete5_users.csv',
        ];
        $app = $this->app;
        $config = $this->app->make('config');
        $bom = $config->get('concrete.export.csv.include_bom') ? $config->get('concrete.charset_bom') : '';

        return StreamedResponse::create(
            function () use ($app, $result, $bom) {
                $writer = $app->build(
                    UserExporter::class,
                    [
                        'writer' => $this->app->make(WriterFactory::class)->createFromPath('php://output', 'w'),
                    ]
                );
                echo $bom;
                $writer->setUnloadDoctrineEveryTick(50);
                $writer->insertHeaders();
                $writer->insertList($result->getItemListObject());
            },
            200,
            $headers);
    }

    protected function setupUser($uID)
    {
        $me = $this->app->make(User::class);
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
            $this->canActivateUser = $this->canEdit && $tp->canActivateUser() && $me->getUserID() != $ui->getUserID();
            $this->canEditAvatar = $this->canEdit && $this->assignment->allowEditAvatar();
            $this->canEditUserName = $this->canEdit && $this->assignment->allowEditUserName();
            $this->canEditLanguage = $this->canEdit && $this->assignment->allowEditDefaultLanguage();
            $this->canEditTimezone = $this->canEdit && $this->assignment->allowEditTimezone();
            $this->canEditEmail = $this->canEdit && $this->assignment->allowEditEmail();
            $this->canEditPassword = $this->canEdit && $this->assignment->allowEditPassword();
            $this->canSignInAsUser = $this->canEdit && $tp->canSudo() && $me->getUserID() != $ui->getUserID();
            $this->canDeleteUser = $this->canEdit && $tp->canDeleteUser() && $me->getUserID() != $ui->getUserID();
            $this->canAddGroup = $this->canEdit && $tp->canAccessGroupSearch();
            $this->allowedEditAttributes = [];
            if ($this->canEdit) {
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
}
