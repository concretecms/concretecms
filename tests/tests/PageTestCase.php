<?php

abstract class PageTestCase extends ConcreteDatabaseTestCase
{
    protected $fixtures = array();
    protected $tables = [
        'Pages',
        'PageThemes',
        'PermissionKeys',
        'PermissionKeyCategories',
        'PageTypes',
        'Collections',
        'CollectionVersions',
        'CollectionVersionFeatureAssignments',
        'CollectionVersionBlockStyles',
        'CollectionVersionThemeCustomStyles',
        'CollectionVersionRelatedEdits',
        'CollectionVersionAreaStyles',
        'PermissionAccessEntityTypes',
        'PagePermissionAssignments',
        'CollectionVersionBlocks',
        'Areas',
        'PageSearchIndex',
        'ConfigStore',
        'GatheringDataSources',
        'Logs',
        'PageTypePublishTargetTypes',
        'AttributeKeyCategories',
        'PageTypeComposerOutputBlocks',
        'PageTypeComposerFormLayoutSets'
    ]; // so brutal

    protected $metadatas = array(
        'Concrete\Core\Entity\Site\Site',
        'Concrete\Core\Entity\Site\Locale',
        'Concrete\Core\Entity\Site\Type',
        'Concrete\Core\Entity\Site\Tree',
        'Concrete\Core\Entity\Site\SiteTree',
        'Concrete\Core\Entity\Page\Relation\MultilingualRelation',
        'Concrete\Core\Entity\Page\Relation\SiblingRelation',
        'Concrete\Core\Entity\Page\PagePath',
        'Concrete\Core\Entity\Page\Template',
        'Concrete\Core\Entity\Attribute\Key\PageKey',
        'Concrete\Core\Entity\Attribute\Value\PageValue',
        'Concrete\Core\Entity\Attribute\Value\Value',
        'Concrete\Core\Entity\Attribute\Key\Key',
    );

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $service = \Core::make('site/type');
        if (!$service->getDefault()) {
            $service->installDefault();
        }

        $service = \Core::make('site');
        if (!$service->getDefault()) {
            $service->installDefault();
        }

        Page::addHomePage();
        PageTemplate::add('full', 'Full');
        PageType::add(array(
            'handle' => 'basic',
            'name' => 'Basic',
        ));
    }

    public function setUp()
    {
        parent::setUp();
    }

    protected static function createPage($name, $parent = false, $type = false, $template = false)
    {
        if ($parent === false) {
            $parent = Page::getByID(HOME_CID);
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

        $page = $parent->add($pt, array(
            'cName' => $name,
            'pTemplateID' => $template->getPageTemplateID(),
        ));

        return $page;
    }
}
