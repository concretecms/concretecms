<?php

namespace Concrete\TestHelpers\Page;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Encryption\PasswordHasher;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Permission\Access\Entity\Type as AccessEntityType;
use Concrete\Core\Permission\Category as PermissionCategory;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Support\Facade\Application as ApplicationFacade;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\User;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ServerInterface;
use Concrete\TestHelpers\Page\PageTestCase;
use Symfony\Component\HttpFoundation\Response;

class DashboardPageTestCase extends PageTestCase
{
    /** @var string|null */
    protected static $pageUrl;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $app = ApplicationFacade::getFacadeApplication();

        Category::add('user');
        Category::add('collection');
        AccessEntityType::add('page_owner', 'Page Owner');
        AccessEntityType::add('group', 'Group');
        PermissionCategory::add('page');
        PermissionKey::add('page', 'view_page', 'View Page', '', 0, 0);
        PermissionKey::add('page', 'view_page_versions', 'View Page Versions', '', 0, 0);
        PermissionKey::add('page', 'edit_page_contents', 'Edit Page Contents', '', 0, 0);
        PermissionKey::add('page', 'edit_page_properties', 'Edit Page Properties', '', 0, 0);

        $hasher = $app->make(PasswordHasher::class);
        $adminInfo = UserInfo::addSuperUser(
            $hasher->hashPassword('p4ssW0!rd'),
            'admin@example.org'
        );
        // Persist the admin login
        $admin = $adminInfo->getUserObject();
        $admin->persist(false);

        if (!isset(static::$pageUrl)) {
          throw new \Exception("Please set the page URL for the test.");
        }

        $login = SinglePage::add(static::$pageUrl);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        $app = ApplicationFacade::getFacadeApplication();
        $app->forgetInstance(User::class);

        $session = $app->make('session');
        $session->clear();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            // General
            'Config',
            // Pages
            'PageThemeCustomStyles',
            // Users & permissions
            'UserGroups',
            'Groups',
            'TreeTypes',
            'TreeNodes',
            'TreeNodePermissionAssignments',
            'AreaPermissionAssignments',
            'PermissionAccess',
            'PermissionAccessEntities',
            'PermissionAccessEntityGroups',
            'PermissionAccessList',
            'PermissionKeyCategories',
            'PermissionKeys',
            'TreeNodeTypes',
            'Trees',
            'TreeGroupNodes',
            // Blocks
            'btCoreStackDisplay',
            'Blocks',
            // Stacks
            'Stacks',
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            // Users
            'Concrete\Core\Entity\User\User',
            'Concrete\Core\Entity\User\UserSignup',
            'Concrete\Core\Entity\Attribute\Category',
            'Concrete\Core\Entity\Attribute\Type',
            'Concrete\Core\Entity\Attribute\Key\Key',
            'Concrete\Core\Entity\Attribute\Key\UserKey',
            // Messenger
            'Concrete\Core\Entity\Command\Process',
            'Concrete\Core\Entity\Command\TaskProcess',
            // Blocks
            'Concrete\Core\Entity\Block\BlockType\BlockType',
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $session = $this->app->make('session');
        $session->getFlashBag()->clear();
    }

    protected function getCookies(): array
    {
        $cookieName = $this->app['config']->get('concrete.session.name');
        $session = $this->app['session'];

        return [$cookieName => $session->getId()];
    }

    protected function sendRequest(Request $request): Response
    {
        // Make the request variables available through the PHP defaults that
        // concrete5 controllers are using
        $request->overrideGlobals();

        $server = $this->app->make(ServerInterface::class);

        // The controller "sends" the request, i.e. prints it to the current
        // PHP thread STDOUT which is why we want to hide this from the test
        // output.
        ob_start();
        $response = $server->handleRequest($request);
        $contents = ob_get_contents();
        ob_end_clean();

        return $response;
    }

    protected function assertFlashMessage($type, $message): void
    {
        $session = $this->app->make('session');
        $pageMessages = $session->getFlashBag()->get('page_message');
        if (!is_array($pageMessages)) {
            $pageMessages = [];
        }
        $storedMessage = ['undefined', 'undefined', false];
        foreach ($pageMessages as $msg) {
            if ($msg[0] === $type) {
                $storedMessage = $msg;
                break;
            }
        }

        $this->assertEquals($type, $storedMessage[0]);
        $this->assertEquals($message, $storedMessage[1]);
    }
}
