<?php

namespace Concrete\Controller\SinglePage\Dashboard\Users;

use Concrete\Controller\Element\Search\Users\Header;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Csv\Export\UserExporter;
use Concrete\Core\Csv\WriterFactory;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Search\SavedUserSearch;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Navigation\Breadcrumb\Dashboard\DashboardUserBreadcrumbFactory;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Url;
use Concrete\Core\User\Command\UpdateUserAvatarCommand;
use Concrete\Core\User\Component\AvatarCropperInstanceFactory;
use Concrete\Core\User\EditResponse as UserEditResponse;
use Concrete\Core\User\User;
use Concrete\Core\Workflow\Progress\UserProgress as UserWorkflowProgress;
use Exception;
use Imagine\Image\Box;
use PermissionKey;
use Permissions;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
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
use Concrete\Core\Session\SessionValidator;
use IPLib\Factory;
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
     * @var bool
     */
    protected $canResetPassword;

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
        $query = Url::createFromServer($_SERVER)->getQuery();

        $exportArgs = [$this->getPageObject()->getCollectionPath(), 'csv_export'];
        if ($this->getAction() == 'advanced_search') {
            $exportArgs[] = 'advanced_search';
        }
        if ($this->getAction() == 'preset') {
            $exportArgs[] = 'preset';
            $query->set(['presetID' => array_first($this->getParameters())]);
        }
        $exportURL = $this->app->make('url/resolver/path')->resolve($exportArgs);
        $exportURL = $exportURL->setQuery($query);
        $headerMenu->getElementController()->setExportURL($exportURL);

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

    public function delete_avatar($uID = false)
    {
        $this->setupUser($uID);
        $token = $this->token;
        if (!$token->validate('delete_avatar', $this->request->query->get('ccm_token'))) {
            $this->error->add($token->getErrorMessage());
        }
        if ($this->error->has()) {
            return new JsonResponse($this->error);
        } else {
            $service = $this->app->make('user/avatar');
            $service->removeAvatar($this->user);
            return new JsonResponse($this->user);
        }
    }

    public function update_avatar($uID = false)
    {
        $this->setupUser($uID);
        $instanceFactory = $this->app->make(AvatarCropperInstanceFactory::class);
        $instance = $instanceFactory->createInstanceFromRequest($this->request);
        if (!$instanceFactory->instanceMatchesAccessToken($instance, $this->request->get('accessToken') ?? '')) {
            $result['error'] = true;
            $result['message'] = $this->token->getErrorMessage();
            return new JsonResponse($result, 400);
        }

        if ($this->canEditAvatar) {
            $result = [];
            $file = $this->request->files->get('file');
            if ($file !== null) {

                $command = new UpdateUserAvatarCommand($this->user, $file);
                $this->app->executeCommand($command);

                // Update the result
                $result['success'] = true;
                $result['avatar'] = $this->user->getUserAvatar()->getPath() . '?' . time();

            }

            $this->flash('success', t('Profile picture saved.'));
            return new JsonResponse($result, $result['success'] ? 200 : 400);

        } else {
            throw new Exception(t('Access Denied.'));
        }
    }

    public function update_status($task = null, $uID = false, $token = null)
    {
        switch ($task) {
            case 'activate':
                $this->setupUser($uID);
                if ($this->canActivateUser && $this->app->make('helper/validation/token')->validate()) {
                    if ($this->user->triggerActivate()) {
                        $mh = $this->app->make('helper/mail');
                        $mh->to($this->user->getUserEmail());
                        $config = $this->app->make('config');
                        if ($config->get('concrete.email.register_notification.address')) {
                            if ($config->get('concrete.email.register_notification.name')) {
                                $fromName = $config->get('concrete.email.register_notification.name');
                            } else {
                                $fromName = t('Website Registration Notification');
                            }
                            $mh->from($config->get('concrete.email.register_notification.address'), $fromName);
                        } else {
                            $mh->from($config->get('concrete.email.default.address'), t('Website Registration Notification'));
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
                if ($this->canActivateUser && $this->app->make('helper/validation/token')->validate('', $token)) {
                    $this->user->markValidated();
                    $this->user->triggerActivate('register_activate', USER_SUPER_ID);
                    $this->redirect('/dashboard/users/search', 'edit', $this->user->getUserID(), 'email_validated');
                }
                break;
            case 'send_email_validation':
                $this->setupUser($uID);
                if ($this->canActivateUser && $this->app->make('helper/validation/token')->validate('', $token)) {
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
            case 'reset_password':
                $this->setupUser($uID);
                if ($this->canResetPassword && $this->app->make('helper/validation/token')->validate()) {
                    $this->user->markAsPasswordReset();
                    $this->flash('success', t('The user will have to change their password at next login.'));
                    return $this->buildRedirect(['/dashboard/users/search', 'edit', $this->user->getUserID()]);
                }
            case 'delete':
                $this->setupUser($uID);
                if ($this->canDeleteUser && $this->app->make('helper/validation/token')->validate()) {
                    $wasDeleted = $this->user->triggerDelete($this->user);
                    if ($wasDeleted) {
                        $this->flash('success', t("User deleted successfully"));
                        return $this->buildRedirect('/dashboard/users/search');
                    } else {
                        $this->redirect('/dashboard/users/search', 'edit', $uID, 'deleted');
                    }
                }
                break;
        }
        $this->view($uID);
    }

    public function save_account($uID = null)
    {
        $error = new ErrorList();
        $this->setupUser($uID);
        if ($this->user && $this->canEdit) {
            $userMessage = new UserEditResponse();
            if (!$this->app->make('helper/validation/token')->validate('save_account')) {
                $error->add($this->app->make('helper/validation/token')->getErrorMessage());
            }

            $data = [];
            if ($this->canEditUserName) {
                $username = $this->request->request->get('uName');
                $this->app->make('validator/user/name')->isValidFor($username, $this->user, $error);
                $data['uName'] = $username;
            }
            if ($this->canEditEmail) {
                $email = $this->request->request->get('uEmail');
                $this->app->make('validator/user/email')->isValidFor($email, $this->user, $error);
                $data['uEmail'] = $email;
            }
            if ($this->app->make('config')->get('concrete.misc.user_timezones') && $this->canEditTimezone) {
                $timezone = $this->request->request->get('uTimezone');
                $data['uTimezone'] = $timezone;
            }
            if ($this->app->make('config')->get('concrete.misc.user_timezones') && $this->canEditTimezone) {
                $timezone = $this->request->request->get('uTimezone');
                $data['uTimezone'] = $timezone;
            }
            $languages = Localization::getAvailableInterfaceLanguages();
            if (count($languages) > 0 && $this->canEditLanguage) {
                $language = $this->request->request->get('uDefaultLanguage');
                $data['uDefaultLanguage'] = $language;
            }
            if ($this->canEditHomeFileManagerFolderID) {
                $uHomeFileManagerFolderID = $this->request->request->get('uHomeFileManagerFolderID');
                if ($uHomeFileManagerFolderID == 0) {
                    $uHomeFileManagerFolderID = '';
                }

                $data['uHomeFileManagerFolderID'] = $uHomeFileManagerFolderID;
            }

            if ($this->canEditPassword && !empty($this->request->request->get('uPasswordNew'))) {
                $passwordMine = (string) $this->request->request->get('uPasswordMine');
                $passwordNew = $this->request->request->get('uPasswordNew');
                $passwordNewConfirm = $this->request->request->get('uPasswordNewConfirm');

                $this->app->make('validator/password')->isValidFor($passwordNew, $this->user, $error);

                if ($passwordNew) {

                    $me = $this->app->make(User::class)->getUserInfoObject();
                    if (!$me->passwordMatches($passwordMine)) {
                        $error->add(t('Your password is invalid.'));
                    }
                    if ($passwordNew != $passwordNewConfirm) {
                        $error->add(t('The two passwords provided do not match.'));
                    }
                }
                $data['uPasswordConfirm'] = $passwordNew;
                $data['uPassword'] = $passwordNew;
            }
            if ($this->shouldViewIgnoredIPMismatches() && $this->canEditIgnoredIPMismatches()) {
                $ignoredIPMismatches = [];
                foreach (preg_split('/\s+/', (string) $this->request->request->get('ignoredIPMismatches'), -1, PREG_SPLIT_NO_EMPTY) as $ignoredIPMismatch) {
                    $range = Factory::parseRangeString($ignoredIPMismatch);
                    if ($range === null) {
                        $error->add(t('The IP address range %s is not valid.', $ignoredIPMismatch));
                    } else {
                        $range = (string) $range;
                        if (!in_array($range, $ignoredIPMismatches, true)) {
                            $ignoredIPMismatches[] = $range;
                        }
                    }
                }
                $data['ignoredIPMismatches'] = $ignoredIPMismatches;
            }

            $userMessage->setError($error);
            if (!$error->has()) {
                $this->user->update($data);
                $message[] = t('User updated successfully.');
                if (!empty($password)) {
                    $message[] = t('Password changed successfully.');
                }
                $this->flash('success', implode(' ', $message));
            }

            return new JsonResponse($userMessage);

        } else {
            $error->add(t('Invalid user.'));
            return new JsonResponse($error);
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

            $this->set('pageTitle', t('View User'));

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
            $headerMenu->set('canResetPassword', $this->canResetPassword);
            $headerMenu->set('canDeleteUser', $this->canDeleteUser);
            $headerMenu->set('validateEmailRegistration', (bool) $this->app->make('config')->get('concrete.user.registration.validate_email'));
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
                    }
                    break;
            }

            $factory = $this->createBreadcrumbFactory();
            $this->setBreadcrumb($factory->getBreadcrumb($this->getPageObject(), $ui));
            $this->render("/dashboard/users/search/edit");
        } else {
            return $this->buildRedirect('/dashboard/users/search');
        }


    }

    public function view($uID = null, $status = false)
    {
        if (isset($uID)) {
            return $this->edit($uID, $status);
        }

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
    public function csv_export($searchMethod = null)
    {
        $permissions = new Checker();
        if ($permissions->canAccessUserSearchExport()) {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=concrete_users.csv',
            ];
            $app = $this->app;
            $config = $this->app->make('config');
            $bom = $config->get('concrete.export.csv.include_bom') ? $config->get('concrete.charset_bom') : '';

            if ($searchMethod === 'advanced_search') {
                $query = $this->getQueryFactory()->createFromAdvancedSearchRequest(
                    $this->getSearchProvider(),
                    $this->request,
                    Request::METHOD_GET
                );
            } elseif ($searchMethod === 'preset') {
                $presetID = $this->request->query->get('presetID');
                $preset = $this->entityManager->find(SavedUserSearch::class, $presetID);
                if ($preset) {
                    $query = $this->getQueryFactory()->createFromSavedSearch($preset);
                } else {
                    throw new UserMessageException(t('Invalid search preset.'));
                }
            }
            else {
                $query = $this->getQueryFactory()->createQuery(
                    $this->getSearchProvider(),
                    [
                        $this->getSearchKeywordsField()
                    ]
                );
            }

            $result = $this->createSearchResult($query);

            return new StreamedResponse(
                function () use ($app, $result, $bom) {
                    $writer = $app->make(
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
                $headers
            );
        } else {
            throw new UserMessageException(t('Access Denied.'));
        }
    }

    private function getFolderList()
    {
        $folderList = [];

        /** @var Connection $db */
        $db = $this->app->make(Connection::class);

        // fetch all folders from database
        $rows = $db->fetchAll("SELECT tn.treeNodeId, tn.treeNodeName FROM TreeNodes AS tn LEFT JOIN TreeNodeTypes AS tnt ON (tn.treeNodeTypeID = tnt.treeNodeTypeID) WHERE tnt.treeNodeTypeHandle = 'file_folder' AND tn.treeNodeName != ''");

        foreach ($rows as $row) {
            $folderList[$row["treeNodeId"]] = $row["treeNodeName"];
        }

        return $folderList;
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
            $this->canResetPassword = $this->canEdit && $tp->canActivateUser();
            $this->canEditAvatar = $this->canEdit && $this->assignment->allowEditAvatar();
            $this->canEditUserName = $this->canEdit && $this->assignment->allowEditUserName();
            $this->canEditLanguage = $this->canEdit && $this->assignment->allowEditDefaultLanguage();
            $this->canEditTimezone = $this->canEdit && $this->assignment->allowEditTimezone();
            $this->canEditEmail = $this->canEdit && $this->assignment->allowEditEmail();
            $this->canEditPassword = $this->canEdit && $this->assignment->allowEditPassword();
            $this->canEditHomeFileManagerFolderID = $this->canEdit && $this->assignment->allowEditHomeFileManagerFolderID();
            $this->canSignInAsUser = $this->canEdit && $tp->canSudo() && $me->getUserID() != $ui->getUserID();
            $this->canDeleteUser = $this->canEdit && $tp->canDeleteUser() && $me->getUserID() != $ui->getUserID();
            $this->canAddGroup = $this->canEdit && $tp->canAccessGroupSearch();
            $this->allowedEditAttributes = [];
            if ($this->canEdit) {
                $this->allowedEditAttributes = $this->assignment->getAttributesAllowedArray();
            }
            $folderList = ['' => t("** None")] + $this->getFolderList();
            $this->set('user', $ui);
            $this->set('folderList', $folderList);
            $this->set('canEditAvatar', $this->canEditAvatar);
            $this->set('canEditUserName', $this->canEditUserName);
            $this->set('canEditEmail', $this->canEditEmail);
            $this->set('canEditPassword', $this->canEditPassword);
            $this->set('canEditHomeFileManagerFolderID', $this->canEditHomeFileManagerFolderID);
            $this->set('canEditTimezone', $this->canEditTimezone);
            $this->set('canEditLanguage', $this->canEditLanguage);
            $this->set('canActivateUser', $this->canActivateUser);
            $this->set('canSignInAsUser', $this->canSignInAsUser);
            $this->set('canDeleteUser', $this->canDeleteUser);
            $this->set('canViewAccountModal', $this->canEditUserName || $this->canEditEmail || $this->canEditPassword
                || $this->canEditLanguage || $this->canEditTimezone);
            $this->set('allowedEditAttributes', $this->allowedEditAttributes);
            $this->set('canAddGroup', $this->canAddGroup);
            $this->set('shouldViewIgnoredIPMismatches', $this->shouldViewIgnoredIPMismatches());
            $this->set('canEditIgnoredIPMismatches', $this->canEditIgnoredIPMismatches());
        }
    }

    protected function shouldViewIgnoredIPMismatches(): bool
    {
        $config = $this->app->make(Repository::class);

        return (bool) $config->get(SessionValidator::CONFIGKEY_IP_MISMATCH) && (bool) $config->get(SessionValidator::CONFIGKEY_ENABLE_USERSPECIFIC_IP_MISMATCH_ALLOWLIST);
    }

    protected function canEditIgnoredIPMismatches(): bool
    {
        $page = Page::getByPath('/dashboard/system/registration/automated_logout');
        if (!$page || $page->isError()) {
            return false;
        }
        $permissions = new Checker($page);

        return $permissions->canViewPage() ? true : false;
    }
}
