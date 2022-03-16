<?php

namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Package;
use Concrete\Core\Support\Manager as CoreManager;

defined('C5_EXECUTE') or die('Access Denied.');

class Manager extends CoreManager
{
    public function __construct(Application $application)
    {
        parent::__construct($application);
    }

    public function createAuthenticationTypeDriver()
    {
        return new AuthenticationType();
    }

    public function createContentEditorSnippetDriver()
    {
        return new ContentEditorSnippet();
    }

    public function createConversationRatingTypeDriver()
    {
        return new ConversationRatingType();
    }

    public function createJobDriver()
    {
        return new Job();
    }

    public function createPermissionKeyDriver()
    {
        return new PermissionKey();
    }

    public function createWorkflowTypeDriver()
    {
        return new WorkflowType();
    }

    public function createStorageLocationTypeDriver()
    {
        return new StorageLocationType();
    }

    public function createExternalFileProviderTypeDriver()
    {
        return new ExternalFileProviderType();
    }

    public function createImageEditorDriver()
    {
        return new ImageEditor();
    }

    public function createAntispamLibraryDriver()
    {
        return new AntispamLibrary();
    }

    public function createAttributeSetDriver()
    {
        return $this->app->make('Concrete\Core\Package\ItemCategory\AttributeSet');
    }

    public function createCaptchaLibraryDriver()
    {
        return new CaptchaLibrary();
    }

    public function createGeolocatorLibraryDriver()
    {
        return new GeolocatorLibrary();
    }

    public function createGroupSetDriver()
    {
        return new GroupSet();
    }

    public function createGroupDriver()
    {
        return new Group();
    }

    public function createAttributeKeyCategoryDriver()
    {
        return new AttributeKeyCategory();
    }

    public function createPermissionAccessEntityTypeDriver()
    {
        return new PermissionAccessEntityType();
    }

    public function createPermissionKeyCategoryDriver()
    {
        return new PermissionKeyCategory();
    }

    public function createWorkflowProgressCategoryDriver()
    {
        return new WorkflowProgressCategory();
    }

    public function createPageTypePublishTargetTypeDriver()
    {
        return new PageTypePublishTargetType();
    }

    public function createPageTypeComposerControlTypeDriver()
    {
        return new PageTypeComposerControlType();
    }

    public function createPageTypeDriver()
    {
        return new PageType();
    }

    public function createPageTemplateDriver()
    {
        return new PageTemplate();
    }

    public function createMailImporterDriver()
    {
        return new MailImporter();
    }

    public function createAttributeTypeDriver()
    {
        return $this->app->make('Concrete\Core\Package\ItemCategory\AttributeType');
    }

    public function createAttributeKeyDriver()
    {
        return $this->app->make('Concrete\Core\Package\ItemCategory\AttributeKey');
    }

    public function createSinglePageDriver()
    {
        return new SinglePage();
    }

    public function createBlockTypeSetDriver()
    {
        return new BlockTypeSet();
    }

    public function createBlockTypeDriver()
    {
        return new BlockType();
    }

    public function createThemeDriver()
    {
        return new Theme();
    }

    public function createWorkflowDriver()
    {
        return new Workflow();
    }

    public function createExpressEntityDriver()
    {
        return $this->app->make(ExpressEntity::class);
    }

    public function createSiteTypeDriver()
    {
        return $this->app->make(SiteType::class);
    }

    public function createIpAccessControlCategoryDriver()
    {
        return $this->app->make(IpAccessControlCategory::class);
    }

    public function createTaskSetDriver()
    {
        return $this->app->make(TaskSet::class);
    }

    public function createTaskDriver()
    {
        return $this->app->make(Task::class);
    }

    public function createContainerDriver()
    {
        return $this->app->make(Container::class);
    }

    public function getPackageItems(Package $package)
    {
        $items = [];
        foreach ($this->getPackageItemCategories() as $category) {
            /**
             * @var ItemInterface
             */
            $items = array_merge($items, $category->getItems($package));
        }

        return $items;
    }

    public function getPackageItemCategories()
    {
        $return = [];
        foreach ($this->app['config']->get('app.package_items') as $item) {
            $return[] = $this->driver($item);
        }

        return $return;
    }
}
