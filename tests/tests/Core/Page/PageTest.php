<?php

class PageTest extends ConcreteDatabaseTestCase {

    protected $fixtures = array();
    protected $tables = array('Pages', 'PermissionKeys', 'PermissionKeyCategories', 'PageTypes',
        'PageTemplates', 'Collections', 'CollectionVersions', 'Config');

    public function setUp() {
        parent::setUp();
        Page::addHomePage();
        PageTemplate::add('full', 'Full');
        PageType::add(array(
            'handle' => 'basic',
            'name' => 'Basic'
        ));
    }

    public function testBasicCreatePage()
    {
        $home = Page::getByID(HOME_CID);
        $pt = PageType::getByID(1);
        $template = PageTemplate::getByID(1);
        $page = $home->add($pt, array(
            'uID'=>1,
            'cName'=> 'Test page',
            'pTemplateID' => $template->getPageTemplateID()
        ));
        $this->assertTrue($page instanceof Page);
        $this->assertEquals($page->getCollectionParentID(), 1);
        $this->assertEquals($page->getPageTemplateID(), 1);
        $this->assertEquals($page->getPageTypeID(), 1);
        $this->assertEquals($page->getVersionID(), 1);
    }

    public function testCreatePageFail()
    {
        $badPage = Page::getByID(42069);
        try {
            $page = $badPage->add($ct,array(
                    'uID'=>1,
                    'cName' => 'Stupid Page',
                    'cHandle'=> 'stupid-page'
                ));
        } catch(Exception $e) {
            $caught = true;
        }

        if(!$caught) {
            $this->fail('Added a page to a non-page');
        }

    }
}