<?php


/** 
 * Tests
 *! a. Core block, no override, render view, proper view.php shows
 *! b. Core block, no override, render add, proper add.php shows up
 * c. Core Block, no override, add block to system, proper data is saved - Handled by ConcreteBlockTest and individual block (TODO)
 * d. Core Block, no override, add block to system, render edit, proper edit.php shows - Handled by ConcreteBlockTest and individual block (TODO)
 *! e. Core Block, no override, render view, with custom template file in templates/ in core space
 *! f. Core Block, no override, render view, with custom template directory in templates/ in core space (with view.php inside that directory)
 *! g. Core Block, no override, render view, with view.css and view.js header items
 *! h. Core Block, no override, render view, with js/ and css/ directories and several items in each
 *! i. Core Block, no override, render view, with custom template file in templates/ in core space, with view.css and view.js
 *! j. Core Block, no override, render view, with custom template file in templates/ in core space, with js/ and css/ directories and several items in each
 *! k. Core Block, no override, render view, with custom template file in a package, in the local space, proper view.php shows
 * l. k + proper view.css and view.js are loaded from core directory, then the same packages directory
 * m. k + custom template directory in package, loading view.css from template directory, two javascripts from js/ directory.
 */
class TemplateTests extends ConcreteTestCase
{
    public function TemplateTests()
    {
        $this->UnitTestCase('Template Tests');
    }

    public function testCoreImageBlockWithNoOverridesHasCorrectTemplates()
    {
        // Test a.

        $p = Page::getByID(1);
        $blocks = $p->getBlocks('Header');
        $b = $blocks[0];
        $bvt = new BlockViewTemplate($b);
        $this->assertEqual($bvt->getTemplate(), DIR_BASE_CORE . '/blocks/image/view.php', 'Test A');

        // Test b.
        $bt = BlockType::getByHandle('image');
        global $a, $ap, $c, $cp;
        if (is_object($bt)) {
            ob_start();
            $bv = new BlockView();
            $bv->render($bt, 'add', array('a' => $a, 'ap' => $ap, 'c' => $c, 'cp' => $cp));
            ob_end_clean();
        }

        $this->assertEqual($bv->getTemplate(), DIR_BASE_CORE . '/blocks/image/add.php', 'Test B');
    }

    public function testCoreBlockWithCustomTemplateFile()
    {

        // TEST E
        $p = Page::getByID(1);
        $blocks = $p->getBlocks('Header');
        $b = $blocks[0];
        $b->setCustomTemplate('test_template.php');

        $p = Page::getByID(1);
        $blocks = $p->getBlocks('Header');
        $b = $blocks[0];

        // empty image directory doesn't fuck up view
        mkdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates/test_template.php');
        $bvt = new BlockViewTemplate($b);
        $this->assertEqual($bvt->getTemplate(), DIR_FILES_BLOCK_TYPES_CORE . '/image/templates/test_template.php', 'Test E');
        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates/test_template.php');
        rmdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates');
        $b->setCustomTemplate(false);
    }

    public function testCoreBlockWithCustomTemplateDirectory()
    {

        // TEST F
        $p = Page::getByID(1);
        $blocks = $p->getBlocks('Header');
        $b = $blocks[0];
        $b->setCustomTemplate('test_template_directory');

        $p = Page::getByID(1);
        $blocks = $p->getBlocks('Header');
        $b = $blocks[0];

        // empty image directory doesn't fuck up view
        mkdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates');
        mkdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates/test_template_directory');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates/test_template_directory/view.php');
        $bvt = new BlockViewTemplate($b);
        $this->assertEqual($bvt->getTemplate(), DIR_FILES_BLOCK_TYPES_CORE . '/image/templates/test_template_directory/view.php', 'Test F');
        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates/test_template_directory/view.php');
        rmdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates/test_template_directory');
        rmdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates');
        $b->setCustomTemplate(false);
    }

    // OVERRIDES
    /*
    function testCoreImageBlockWithOverrideHasCorrectView() {
        $p = Page::getByID(1);
        $blocks = $p->getBlocks('Header');
        $b = $blocks[0];		
        
        // empty image directory doesn't fuck up view
        mkdir(DIR_FILES_BLOCK_TYPES . '/image');		
        $bvt = new BlockViewTemplate($b);
        $this->assertEqual($bvt->getTemplate(), DIR_BASE_CORE . '/blocks/image/view.php');		
        rmdir (DIR_FILES_BLOCK_TYPES . '/image');

        // local image override w/view.php works
        mkdir(DIR_FILES_BLOCK_TYPES . '/image');
        touch(DIR_FILES_BLOCK_TYPES . '/image/view.php');
        $bvt = new BlockViewTemplate($b);
        $this->assertEqual($bvt->getTemplate(), DIR_FILES_BLOCK_TYPES . '/image/view.php');	
        
        unlink(DIR_FILES_BLOCK_TYPES . '/image/view.php');
        rmdir (DIR_FILES_BLOCK_TYPES . '/image');

        // local image override w/image.php works
        touch(DIR_FILES_BLOCK_TYPES . '/image.php');
        $bvt = new BlockViewTemplate($b);
        $this->assertEqual($bvt->getTemplate(), DIR_FILES_BLOCK_TYPES . '/image.php');		
        unlink(DIR_FILES_BLOCK_TYPES . '/image.php');
    }
    */
}

class TemplateCoreWebTests extends UnitTestCase
{
    public static $_this;

    public function testCoreBlockHeaderItems()
    {
        // Test g.

        self::$_this->btHandle = 'image';
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/view.css');
        phpQuery::browserGet(BASE_URL . DIR_REL . '/index.php', array('TemplateCoreWebTests', '_testHeaderItems'));
        $this->assertEqual(1, self::$_this->headerItem1Count + self::$_this->headerItem2Count, 'Test G - Just CSS');

        self::$_this->headerItem1Count = 0;
        self::$_this->headerItem2Count = 0;

        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/view.js');
        phpQuery::browserGet(BASE_URL . DIR_REL . '/index.php', array('TemplateCoreWebTests', '_testHeaderItems'));
        $this->assertEqual(2, self::$_this->headerItem1Count + self::$_this->headerItem2Count, 'Test G - CSS and JavaScript');

        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/view.css');
        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/view.js');
    }

    public function testCoreBlockHeaderDirectoryItems()
    {
        // Test h
        self::$_this->headerItem1Count = 0;
        self::$_this->headerItem2Count = 0;
        self::$_this->btHandle = 'image';

        mkdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_CSS);
        mkdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_JAVASCRIPT);
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_CSS . '/test1.css');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_CSS . '/test2.css');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_JAVASCRIPT . '/test1.js');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_JAVASCRIPT . '/test2.js');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_JAVASCRIPT . '/test3.js');

        phpQuery::browserGet(BASE_URL . DIR_REL . '/index.php', array('TemplateCoreWebTests', '_testHeaderItemDirectories'));
        $this->assertEqual(5, self::$_this->headerItem1Count + self::$_this->headerItem2Count, 'Test H - Multiple CSS and JS files');

        self::$_this->headerItem1Count = 0;
        self::$_this->headerItem2Count = 0;

        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_CSS . '/test1.css');
        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_CSS . '/test2.css');
        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_JAVASCRIPT . '/test1.js');
        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_JAVASCRIPT . '/test2.js');
        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_JAVASCRIPT . '/test3.js');
        rmdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_CSS);
        rmdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_JAVASCRIPT);
    }

    public function testCoreBlockHeaderItemsWithCustomTemplate()
    {
        self::$_this->headerItem1Count = 0;
        self::$_this->headerItem2Count = 0;
        self::$_this->btHandle = 'image';

        // TEST I
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/view.css');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/view.js');
        $p = Page::getByID(1);
        $blocks = $p->getBlocks('Header');
        $b = $blocks[0];
        $b->setCustomTemplate('test_template.php');

        $p = Page::getByID(1);
        $blocks = $p->getBlocks('Header');
        $b = $blocks[0];

        // empty image directory doesn't fuck up view
        mkdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates/test_template.php');

        phpQuery::browserGet(BASE_URL . DIR_REL . '/index.php', array('TemplateCoreWebTests', '_testHeaderItems'));
        $this->assertEqual(2, self::$_this->headerItem1Count + self::$_this->headerItem2Count, 'Test I - CSS and JavaScript with custom template');

        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates/test_template.php');
        rmdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates');
        $b->setCustomTemplate(false);

        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/view.css');
        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/view.js');
    }

    public function testCoreBlockHeaderDirectoryItemsWithCustomTemplate()
    {
        // Test h
        self::$_this->headerItem1Count = 0;
        self::$_this->headerItem2Count = 0;
        self::$_this->btHandle = 'image';

        mkdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_CSS);
        mkdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_JAVASCRIPT);
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_CSS . '/test1.css');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_CSS . '/test2.css');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_JAVASCRIPT . '/test1.js');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_JAVASCRIPT . '/test2.js');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_JAVASCRIPT . '/test3.js');

        $p = Page::getByID(1);
        $blocks = $p->getBlocks('Header');
        $b = $blocks[0];
        $b->setCustomTemplate('test_template.php');

        $p = Page::getByID(1);
        $blocks = $p->getBlocks('Header');
        $b = $blocks[0];

        // empty image directory doesn't fuck up view
        mkdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates/test_template.php');
        phpQuery::browserGet(BASE_URL . DIR_REL . '/index.php', array('TemplateCoreWebTests', '_testHeaderItemDirectories'));
        $this->assertEqual(5, self::$_this->headerItem1Count + self::$_this->headerItem2Count, 'Test J - CSS and JS in Directory with Custom Template');

        self::$_this->headerItem1Count = 0;
        self::$_this->headerItem2Count = 0;
        self::$_this->btHandle = '';

        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates/test_template.php');
        rmdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/templates');

        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_CSS . '/test1.css');
        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_CSS . '/test2.css');
        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_JAVASCRIPT . '/test1.js');
        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_JAVASCRIPT . '/test2.js');
        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_JAVASCRIPT . '/test3.js');
        rmdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_CSS);
        rmdir(DIR_FILES_BLOCK_TYPES_CORE . '/image/' . DIRNAME_JAVASCRIPT);

        $b->setCustomTemplate(false);
    }

    // assumes that test custom template list is present in packages and in db
    public function testCustomTemplateInPackage()
    {
        self::$_this->headerItem1Count = 0;
        self::$_this->headerItem2Count = 0;

        $p = Page::getByID(1);
        $blocks = $p->getBlocks('Header Nav');
        $b = $blocks[0];
        $b->setCustomTemplate('test_template.php');

        $p = Page::getByID(1);
        $blocks = $p->getBlocks('Header Nav');
        $b = $blocks[0];

        touch(DIR_PACKAGES . '/autonav_test_template/blocks/autonav/test_template.php');
        $bvt = new BlockViewTemplate($b);
        $this->assertEqual($bvt->getTemplate(), DIR_PACKAGES . '/autonav_test_template/blocks/autonav/test_template.php', 'Test K - File');
        unlink(DIR_PACKAGES . '/autonav_test_template/blocks/autonav/test_template.php');

        touch(DIR_PACKAGES . '/autonav_test_template/blocks/autonav/templates/test_template.php');
        $bvt = new BlockViewTemplate($b);
        $this->assertEqual($bvt->getTemplate(), DIR_PACKAGES . '/autonav_test_template/blocks/autonav/templates/test_template.php', 'Test K - File in Templates');
        unlink(DIR_PACKAGES . '/autonav_test_template/blocks/autonav/templates/test_template.php');

        $b->setCustomTemplate('test_template');
        $p = Page::getByID(1);
        $blocks = $p->getBlocks('Header Nav');
        $b = $blocks[0];

        mkdir(DIR_PACKAGES . '/autonav_test_template/blocks/autonav/templates/test_template');
        touch(DIR_PACKAGES . '/autonav_test_template/blocks/autonav/templates/test_template/view.php');
        $bvt = new BlockViewTemplate($b);
        $this->assertEqual($bvt->getTemplate(), DIR_PACKAGES . '/autonav_test_template/blocks/autonav/templates/test_template/view.php', 'Test K - File in Template Directory');
        unlink(DIR_PACKAGES . '/autonav_test_template/blocks/autonav/templates/test_template/view.php');
        rmdir(DIR_PACKAGES . '/autonav_test_template/blocks/autonav/templates/test_template');

        touch(DIR_PACKAGES . '/autonav_test_template/blocks/autonav/test_template.php');
        $b->setCustomTemplate('test_template.php');
        self::$_this->btHandle = 'autonav';
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/autonav/view.css');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/autonav/view.js');
        phpQuery::browserGet(BASE_URL . DIR_REL . '/index.php', array('TemplateCoreWebTests', '_testHeaderItems'));
        $this->assertEqual(2, self::$_this->headerItem1Count + self::$_this->headerItem2Count, 'Test L - Loading Packaged Template with Core Header Items autoloaded');
        unlink(DIR_PACKAGES . '/autonav_test_template/blocks/autonav/test_template.php');
        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/autonav/view.css');
        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/autonav/view.js');
        $b->setCustomTemplate(false);
        self::$_this->btHandle = '';

        $b->setCustomTemplate('test_template.php');

        self::$_this->headerItem1Count = 0;
        self::$_this->headerItem2Count = 0;

        touch(DIR_PACKAGES . '/autonav_test_template/blocks/autonav/test_template.php');
        mkdir(DIR_FILES_BLOCK_TYPES_CORE . '/autonav/css');
        mkdir(DIR_FILES_BLOCK_TYPES_CORE . '/autonav/js');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/autonav/css/test1.css');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/autonav/css/test2.css');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/autonav/js/test2.js');
        touch(DIR_FILES_BLOCK_TYPES_CORE . '/autonav/js/test9.js');

        self::$_this->btHandle = 'autonav';
        phpQuery::browserGet(BASE_URL . DIR_REL . '/index.php', array('TemplateCoreWebTests', '_testHeaderItemDirectories'));
        $this->assertEqual(4, self::$_this->headerItem1Count + self::$_this->headerItem2Count, 'Test L - Loading Packaged Template with Core Header Items autoloaded from directories');

        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/autonav/css/test1.css');
        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/autonav/css/test2.css');
        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/autonav/js/test2.js');
        unlink(DIR_FILES_BLOCK_TYPES_CORE . '/autonav/js/test9.js');
        rmdir(DIR_FILES_BLOCK_TYPES_CORE . '/autonav/css');
        rmdir(DIR_FILES_BLOCK_TYPES_CORE . '/autonav/js');
        unlink(DIR_PACKAGES . '/autonav_test_template/blocks/autonav/test_template.php');
        self::$_this->btHandle = '';
    }

    public function _testHeaderItems($browser)
    {
        $headerItem1 = $browser->find('link[href=' . DIR_REL . '/concrete/blocks/' . self::$_this->btHandle . '/view.css]');
        $headerItem2 = $browser->find('script[src=' . DIR_REL . '/concrete/blocks/' . self::$_this->btHandle . '/view.js]');

        self::$_this->headerItem1Count = $headerItem1->count();
        self::$_this->headerItem2Count = $headerItem2->count();
    }

    public function _testHeaderItemDirectories($browser)
    {
        $headerItem1 = $browser->find('link');
        $headerItem2 = $browser->find('script');

        foreach ($headerItem1 as $hi) {
            $val = $hi->getAttribute('href');
            if (preg_match(DIR_REL . '\/concrete\/blocks\/' . self::$_this->btHandle . '\/' .DIRNAME_CSS . '\/test(.*).css/i', $val)) {
                ++self::$_this->headerItem1Count;
            }
        }

        foreach ($headerItem2 as $hi) {
            $val = $hi->getAttribute('src');
            if (preg_match(DIR_REL . '\/concrete\/blocks\/' . self::$_this->btHandle . '\/' .DIRNAME_JAVASCRIPT . '\/test(.*).js/i', $val)) {
                ++self::$_this->headerItem2Count;
            }
        }
    }
}
