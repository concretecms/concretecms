<?php
namespace Concrete\Core\Site;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Registry\Applier;
use Concrete\Core\Permission\Registry\Multisite\DefaultFileManagerAssignment;
use Concrete\Core\Permission\Registry\Multisite\DefaultSharedFolderAssignment;
use Concrete\Core\Site\Type\Service as SiteTypeService;
use Concrete\Core\Site\User\Group\Service as UserGroupService;
use Concrete\Core\User\Group\Group;
use Doctrine\ORM\EntityManager;

/**
 * A class that enables multisite support on a single Concrete installation. Responsible for detecting whether
 * multisite is enabled, and responsible for enabling multisite, creating shared folders, etc...
 * @package Concrete\Core\Site
 */
class InstallationService
{
    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * @var Service
     */
    protected $siteService;

    /**
     * @var SiteTypeService
     */
    protected $siteTypeService;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Applier
     */
    protected $permissionsApplier;

    /**
     * @var UserGroupService
     */
    protected $userGroupService;

    public function __construct(
        \Illuminate\Config\Repository $config,
        Service $siteService,
        SiteTypeService $siteTypeService,
        EntityManager $entityManager,
        Filesystem $filesystem,
        Applier $permissionsApplier,
        UserGroupService $userGroupService
    )
    {
        $this->siteService = $siteService;
        $this->siteTypeService = $siteTypeService;
        $this->entityManager = $entityManager;
        $this->filesystem = $filesystem;
        $this->config = $config;
        $this->userGroupService = $userGroupService;
        $this->permissionsApplier = $permissionsApplier;
    }

    /**
     * @return bool
     */
    public function isMultisiteEnabled()
    {
        return (bool) $this->config->get('concrete.multisite.enabled');
    }

    public function enableMultisite()
    {
        $this->populateDefaultSiteTypeData();
        $this->createMultisiteUserGroup();
        $this->createSharedFilesFolders();
        $this->createDefaultNotificationSubscriptions();
        $this->config->save('concrete.multisite.enabled', true);
    }

    public function validateEnvironment()
    {
        $errors = new ErrorList();
        if ($this->isMultisiteEnabled()) {
            $errors->add(t('Multiple site hosting is already enabled.'));
        }

        $defaultSite = $this->siteService->getSite();
        if (!$defaultSite->getSiteCanonicalURL()) {
            $errors->add(t('Your default site must define a canonical URL to enable multiple site hosting.'));
        }


        if ($this->config->get('concrete.permissions.model') == 'simple') {
            $errors->add(t('You must enable advanced permissions to enable multiple site hosting.'));
        }

        if ($this->config->get('concrete.seo.redirect_to_canonical_url')) {
            $errors->add(t('You must disable "Redirect to Canonical URL" in your URL settings page.'));
        }

        return $errors;
    }

    private function populateDefaultSiteTypeData()
    {
        $default = $this->siteTypeService->getDefault();
        $site = $this->siteService->getDefault();
        $home = $site->getSiteHomePageObject();
        $theme = $home->getCollectionThemeObject();

        $default->setSiteTypeThemeID($theme->getThemeID());
        $default->setSiteTypeHomePageTemplateID($home->getPageTemplateID());

        $this->entityManager->persist($default);
        $this->entityManager->flush();
    }

    private function createSharedFilesFolders()
    {
        $root = $this->filesystem->getRootFolder();
        $this->filesystem->addFolder($root, t('Shared Files'));
        $this->permissionsApplier->applyAssignment(new DefaultFileManagerAssignment());
        $this->permissionsApplier->applyAssignment(new DefaultSharedFolderAssignment());
    }

    private function createMultisiteUserGroup()
    {
        $group = $this->userGroupService->getSiteParentGroup();
        if (!$group) {
            Group::add(UserGroupService::PARENT_GROUP_NAME,
                'Parent group for sites hosted in multisite.',
            null);
        }
    }

    /**
     * Takes care of adding "/Sites" to workflow notifications
     */
    private function createDefaultNotificationSubscriptions()
    {
        $sitesGroupEntity = GroupEntity::getOrCreate($this->userGroupService->getSiteParentGroup());
        $pk = Key::getByHandle('notify_in_notification_center');
        $pa = $pk->getPermissionAccessObject();
        $pa->addListItem($sitesGroupEntity);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);

        // This is a hack.
        $db = $this->entityManager->getConnection();
        $db->delete('NotificationPermissionSubscriptionList', [
            'paID' => $pa->getPermissionAccessID(),
            'peID' => $sitesGroupEntity->getAccessEntityID()
        ]);
        $db->delete('NotificationPermissionSubscriptionListCustom', [
            'paID' => $pa->getPermissionAccessID(),
            'peID' => $sitesGroupEntity->getAccessEntityID()
        ]);
        $db->insert('NotificationPermissionSubscriptionList', [
            'paID' => $pa->getPermissionAccessID(),
            'peID' => $sitesGroupEntity->getAccessEntityID(),
            'permission' => 'C'
        ]);
        $db->insert('NotificationPermissionSubscriptionListCustom', [
            'paID' => $pa->getPermissionAccessID(),
            'peID' => $sitesGroupEntity->getAccessEntityID(),
            'nSubscriptionIdentifier' => 'workflow_progress'
        ]);
    }





}
