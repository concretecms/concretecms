<?php
namespace Concrete\Tests\Block;

use Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\UserInfo;
use Concrete\TestHelpers\Page\PageTestCase;
use Doctrine\ORM\EntityManagerInterface;

class BlockRenderTest extends PageTestCase
{
    /** @var \Concrete\Core\Application\Application */
    protected $app;

    /** @var Page */
    protected $c;

    /** @var \Concrete\Core\Page\Collection\Version\Version */
    protected $cv;

    /** @var Area */
    protected $area;

    /** @var BlockType */
    protected $bt;

    protected $btHandle = 'content';

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tables[] = 'Blocks';
        $this->tables[] = 'Config';
        $this->tables[] = 'UserGroups';
        $this->tables[] = 'SystemContentEditorSnippets';
        $this->tables[] = 'btContentLocal';

        $this->metadatas[] = 'Concrete\Core\Entity\Block\BlockType\BlockType';
        $this->metadatas[] = 'Concrete\Core\Entity\User\User';
        $this->metadatas[] = 'Concrete\Core\Entity\User\UserSignup';
        $this->metadatas[] = 'Concrete\Core\Entity\Package';
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $app = Facade::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);

        // Install the theme and make it the site theme.
        $theme = Theme::add('elemental');
        $site = $app->make('site')->getSite();
        $site->setThemeID($theme->getThemeID());
        $em->persist($site);
        $em->flush();
    }

    public function setUp()
    {
        parent::setUp();

        // Add the page and the area
        $this->c = Page::getByID(Page::getHomePageID());
        $this->cv = $this->c->getVersionToModify();
        $this->area = Area::getOrCreate($this->c, 'Main');
        $this->bt = BlockType::installBlockType($this->btHandle);
    }

    public function testBlockNormalRender()
    {
        $blockContent = '<p>Testing content block.</p>';
        $block = $this->cv->addBlock($this->bt, $this->area, [
            'content' => $blockContent,
        ]);

        $bv = new BlockView($block);
        $bv->setAreaObject($this->area);

        ob_start();
        $bv->render('view');
        $contents = ob_get_clean();

        $this->assertEquals($blockContent, trim($contents));
    }

    public function testBlockViewChangingScopeVariables()
    {
        // Add the user
        $ui = UserInfo::add([
            'uName' => 'terry',
            'uEmail' => 'terry@tester.org',
        ]);

        // Make it the "current" user and load the collection to edit mode with
        // that user.
        $req = Request::getInstance();
        $req->setCustomRequestUser($ui);
        $ui->getUserObject()->loadCollectionEdit($this->c);

        // Add the block type and the actual block to the page
        $block = $this->cv->addBlock($this->bt, $this->area, []);

        // Render the block view with the "weird" template which will modify
        // some of the local variables inside the BlockView class.
        $template = DIR_TESTS . '/assets/Block/weird_template.php';

        $view = new BlockView($block);
        $view->start($block);
        $view->setAreaObject($this->area);
        $view->setupRender();
        $view->setViewTemplate($template);

        ob_start();
        $view->renderViewContents($view->getScopeItems());
        $contents = ob_get_clean();

        $this->assertContains(
            'data-block-id="' . $block->getBlockID() . '"',
            $contents
        );
    }
}
