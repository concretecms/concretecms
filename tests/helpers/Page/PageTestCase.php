<?php

namespace Concrete\TestHelpers\Page;


use Concrete\Core\Support\Facade\Application;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Page;
use PageTemplate;
use PageType;

abstract class PageTestCase extends ConcreteDatabaseTestCase
{
    /** @var \Concrete\Core\Application\Application */
    protected $app;

    protected $fixtures = [];
    protected $tables = [
        'Pages',
        'PageThemes',
        'PermissionKeys',
        'PermissionKeyCategories',
        'PageTypes',
        'Collections',
        'CollectionVersions',
        'CollectionVersionBlockStyles',
        'CollectionVersionThemeCustomStyles',
        'CollectionVersionRelatedEdits',
        'CollectionVersionAreaStyles',
        'CollectionVersionBlocksCacheSettings',
        'PermissionAccessEntityTypes',
        'PagePermissionAssignments',
        'CollectionVersionBlocks',
        'Areas',
        'Groups',
        'PageSearchIndex',
        'ConfigStore',
        'Logs',
        'PageTypePublishTargetTypes',
        'AttributeKeyCategories',
        'PageTypeComposerOutputBlocks',
        'PageTypeComposerFormLayoutSets',
        'BlockPermissionAssignments',
    ]; // so brutal

    protected $metadatas = [
        'Concrete\Core\Entity\Site\Type',
        'Concrete\Core\Entity\Site\Site',
        'Concrete\Core\Entity\Site\Locale',
        'Concrete\Core\Entity\Site\SkeletonTree',
        'Concrete\Core\Entity\Site\Tree',
        'Concrete\Core\Entity\Site\SiteTree',
        'Concrete\Core\Entity\Page\Relation\MultilingualRelation',
        'Concrete\Core\Entity\Page\Relation\SiblingRelation',
        'Concrete\Core\Entity\Page\PagePath',
        'Concrete\Core\Entity\Summary\Category',
        'Concrete\Core\Entity\Page\Template',
        'Concrete\Core\Entity\Page\Summary\PageTemplate',
        'Concrete\Core\Entity\User\User',
        'Concrete\Core\Entity\User\UserSignup',
        'Concrete\Core\Entity\Attribute\Key\PageKey',
        'Concrete\Core\Entity\Attribute\Value\PageValue',
        'Concrete\Core\Entity\Attribute\Value\Value',
        'Concrete\Core\Entity\Attribute\Key\Key',
    ];

    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();
        $app = Application::getFacadeApplication();
        $service = $app->make('site/type');
        if (!$service->getDefault()) {
            $service->installDefault();
        }
        $service = $app->make('site');
        // PhP unit and laravel containers mean the request cache is somehow enabled? but only after soo many test runs
        // So we force the cache to be disabled
        $requestCache = $app->make('cache/request');
        $requestCache->disable();
        $service->setCache($requestCache);
        if (!$service->getDefault()) {
            $service->installDefault();
        }
        $site = $service->getDefault();
        Page::addHomePage($site->getSiteTreeObject());
        PageTemplate::add('full', 'Full');
        PageType::add([
            'handle' => 'basic',
            'name' => 'Basic',
        ]);

    }

    public function setUp(): void
    {
        parent::setUp();

        $this->app = Application::getFacadeApplication();
    }

    /**
     * @param string $name
     * @param \Concrete\Core\Page\Page|string|false $parent
     * @param string|int|false $type
     * @param string|int|false $template
     *
     * @return \Concrete\Core\Page\Page
     */
    protected static function createPage($name, $parent = false, $type = false, $template = false)
    {
        if ($parent === false) {
            $parent = Page::getByID(Page::getHomePageID());
        } elseif (is_string($parent)) {
            $parent = Page::getByPath($parent);
        }

        if ($type === false) {
            $type = 1;
        }

        if (is_string($type)) {
            $pt = PageType::getByHandle($type);
        } else {
            $pt = PageType::getByID($type);
        }

        if ($template === false) {
            $template = 'full';
        }

        if (is_string($template)) {
            $template = PageTemplate::getByHandle($template);
        } else {
            $template = PageTemplate::getByID($template);
        }

        $page = $parent->add($pt, [
            'cName' => $name,
            'pTemplateID' => $template->getPageTemplateID(),
        ]);

        return $page;
    }
}
