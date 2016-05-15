<?php

use Concrete\Core\Attribute\Key\Category;

abstract class PageTestCase extends ConcreteDatabaseTestCase
{
    protected $fixtures = array();
    protected $tables = array('Pages', 'PageThemes', 'PagePaths', 'PermissionKeys', 'PermissionKeyCategories', 'PageTypes',
        'Collections', 'CollectionVersions', 'CollectionVersionFeatureAssignments',
        'CollectionVersionBlockStyles', 'CollectionVersionThemeCustomStyles',
        'CollectionVersionRelatedEdits', 'CollectionVersionAreaStyles', 'MultilingualSections', 'MultilingualPageRelations',
        'PagePermissionAssignments', 'CollectionVersionBlocks', 'Areas', 'PageSearchIndex', 'ConfigStore',
        'GatheringDataSources', 'Logs', 'PageTypePublishTargetTypes', 'AttributeKeyCategories',
        'PageTypeComposerOutputBlocks', ); // so brutal

    protected $metadatas = array(
        'Concrete\Core\Entity\Page\Template',
        'Concrete\Core\Entity\Attribute\Key\PageKey',
        'Concrete\Core\Entity\Attribute\Value\PageValue',
        'Concrete\Core\Entity\Attribute\Value\Value',
        'Concrete\Core\Entity\Attribute\Key\Key',
    );

    protected function setUp()
    {
        parent::setUp();
        Page::addHomePage();
        PageTemplate::add('full', 'Full');
        PageType::add(array(
                'handle' => 'basic',
                'name' => 'Basic',
            ));

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
            $template = 1;
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
