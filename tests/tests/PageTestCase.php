<?php
define('ENABLE_BLOCK_CACHE', false);
use \Concrete\Core\Block\View\BlockView;
use Concrete\Core\Attribute\Key\Category;
abstract class PageTestCase extends ConcreteDatabaseTestCase {

    protected $fixtures = array();
    protected $tables = array('Pages', 'PageThemes', 'PagePaths', 'PermissionKeys', 'PermissionKeyCategories', 'PageTypes',
        'PageTemplates', 'Collections', 'CollectionVersions', 'CollectionVersionFeatureAssignments',
        'CollectionAttributeValues', 'CollectionVersionBlockStyles', 'CollectionVersionThemeCustomStyles',
        'CollectionVersionRelatedEdits', 'CollectionVersionAreaStyles', 'CollectionSearchIndexAttributes',
        'PagePermissionAssignments', 'CollectionVersionBlocks', 'Areas', 'PageSearchIndex', 'Config',
        'GatheringDataSources', 'Logs'); // so brutal

    public function setUp() {
        parent::setUp();
        Category::add('collection');
        Page::addHomePage();
        PageTemplate::add('full', 'Full');
        PageType::add(array(
                'handle' => 'basic',
                'name' => 'Basic'
            ));
    }

    protected static function createPage($name, $parent = false)
    {
        if (!is_object($parent)) {
            $parent = Page::getByID(HOME_CID);
        }

        $pt = PageType::getByID(1);
        $template = PageTemplate::getByID(1);
        $page = $parent->add($pt, array(
                'cName'=> $name,
                'pTemplateID' => $template->getPageTemplateID()
            ));
        return $page;
    }

}