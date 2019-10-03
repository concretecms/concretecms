<?php
namespace Concrete\Tests\Block;

use Concrete\Core\Area\Area;
use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\Login\LoginService;
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

        // Basics for the blocks
        $this->tables[] = 'Blocks';
        $this->tables[] = 'Config';
        $this->metadatas[] = 'Concrete\Core\Entity\Block\BlockType\BlockType';
        // Logging in/out the users
        $this->tables[] = 'AuthenticationTypes';
        // General for the users
        $this->metadatas[] = 'Concrete\Core\Entity\User\User';
        $this->metadatas[] = 'Concrete\Core\Entity\User\UserSignup';
        // Adding groups
        $this->tables[] = 'UserGroups';
        $this->tables[] = 'Trees';
        $this->tables[] = 'TreeGroupNodes';
        $this->tables[] = 'TreeTypes';
        // Adding content blocks
        $this->tables[] = 'btContentLocal';
        $this->tables[] = 'SystemContentEditorSnippets';
        // Adding themes
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
        AuthenticationType::add('concrete', 'Concrete');

        // Add the user and enter it to the admin group
        // This will automatically be the superuser as it's the first user
        // entered to the DB.
        $ui = UserInfo::add([
            'uName' => 'terry',
            'uEmail' => 'terry@tester.org',
        ]);

        // Login as that user
        $login = $this->app->make(LoginService::class);
        $login->loginByUserID($ui->getUserID());
        $ui->getUserObject()->setAuthTypeCookie('concrete');

        // Load the collection to edit mode with that user
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

        // Logout the user
        $ui->getUserObject()->logout(true);

        $this->assertContains(
            'data-block-id="' . $block->getBlockID() . '"',
            $contents
        );
    }
}
