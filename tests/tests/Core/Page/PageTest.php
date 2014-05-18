<?php

class PageTest extends ConcreteDatabaseTestCase {

    protected $fixtures = array();
    protected $tables = array('Pages', 'PermissionKeys', 'PermissionKeyCategories', 'PageTypes',
        'PageTemplates', 'Collections', 'CollectionVersions', 'CollectionVersionFeatureAssignments',
        'CollectionAttributeValues', 'CollectionVersionBlockStyles', 'CollectionVersionThemeCustomStyles',
        'CollectionVersionRelatedEdits', 'CollectionVersionAreaStyles', 'CollectionSearchIndexAttributes',
        'PagePermissionAssignments', 'Areas', 'PageSearchIndex', 'Config', 'Logs'); // so brutal

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
        } else {
            $this->assertTrue(true, 'Successful exception on creating a page beneath a nonexistent parent page.');
        }
    }

    public function testPageMove()
    {
        $page1 = self::createPage('Awesome Page');
        $page2 = self::createPage('Awesome Page 2');

        $page1->move($page2);
        $this->assertEquals($page2->getCollectionParentID(), 1);
        $this->assertEquals($page1->getCollectionParentID(), 3);
        $this->assertEquals($page2->getCollectionPath(), '/awesome-page-2');
        $this->assertEquals($page1->getCollectionPath(), '/awesome-page-2/awesome-page');
    }

    public function testDelete()
    {
        $db = Database::get();
        $page1 = self::createPage('Awesome Page');
        $page2 = self::createPage('Awesome Page 2');
        $this->assertEquals(3, $page2->getCollectionID());
        $page2->delete();

        $np = Page::getByID(3);
        $this->assertEquals($np->getCollectionID(), null);

        $pt = PageType::getByID(1);
        $template = PageTemplate::getByID(1);
        $newpage = $page1->add($pt, array(
            'uID'=>1,
            'cName'=> 'Test Sub-page',
            'pTemplateID' => $template->getPageTemplateID()
        ));

        $page1->delete();
        $this->assertEquals(1, $db->GetOne('select count(cID) from Pages'));

        $np1 = Page::getByID(2);
        $np2 = Page::getByID(4);
        $this->assertEquals($np1->getCollectionID(), null);
        $this->assertEquals($np2->getCollectionID(), null);

    }

    protected static function createPage($name)
    {
        $home = Page::getByID(HOME_CID);
        $pt = PageType::getByID(1);
        $template = PageTemplate::getByID(1);
        $page = $home->add($pt, array(
            'cName'=> $name,
            'pTemplateID' => $template->getPageTemplateID()
        ));
        return $page;
    }

    /**
     *  @dataProvider pageNames
     */
    public function testPageNames($name, $special)
    {
        $page = self::createPage($name);
        $parentID = $page->getCollectionParentID();
        $this->assertSame($page->getCollectionName(), $name);
        $th = Loader::helper('text');
        if(!$special) {
            $this->assertSame($page->getCollectionPath(), '/'.$th->urlify($name));
            $this->assertSame($page->getCollectionHandle(), $th->urlify($name));
        } else {
            $this->assertSame($page->getCollectionPath(), '/'.(string)$page->getCollectionID());
            $this->assertSame($page->getCollectionHandle(), '');
        }
        $page->delete();
    }

    public function pageNames() {
        return array(
            array('normal page',false),
            array("awesome page's #spring_break98 !!1! SO COOL",false),
            array('niño borracho',false),
            array('雷鶏',true)
        );
    }

    public function testPageDuplicate()
    {
        $page = self::createPage('double vision');
        $home = Page::getByID(HOME_CID);

        $newPage = $page->duplicate($home);
        $realNewPage = Page::getByID($newPage->getCollectionID(),'ACTIVE');

        $this->assertNotEquals($page->getCollectionID(),$realNewPage->getCollectionID());
        $this->assertEquals($page->getCollectionPath().'-2',$realNewPage->getCollectionPath());
        $this->assertEquals($page->getCollectionName().' 2',$realNewPage->getCollectionName());

        $page->delete();
        $realNewPage->delete();
    }


}