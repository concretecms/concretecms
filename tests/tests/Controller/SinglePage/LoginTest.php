<?php

declare(strict_types=1);

namespace Concrete\Tests\Controller\SinglePage;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Encryption\PasswordHasher;
use Concrete\Core\Entity\Permission\IpAccessControlCategory;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ServerInterface;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use Concrete\Core\Permission\Access\Entity\Type as AccessEntityType;
use Concrete\Core\Permission\Category as PermissionCategory;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Validation\CSRF\Token as CSRFToken;
use Concrete\TestHelpers\Page\PageTestCase;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class LoginTest extends PageTestCase
{
    /**
     * The request instance before the test is run.
     *
     * @var \Concrete\Core\Http\Request
     */
    private $originalRequest;

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
            // Files
            'FileImageThumbnailPaths',
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
            // Logging in/out the users
            'AuthenticationTypes',
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
            'Concrete\Core\Entity\Attribute\Key\Key',
            'Concrete\Core\Entity\Attribute\Key\UserKey',
            // Permissions
            'Concrete\Core\Entity\Permission\IpAccessControlCategory',
            'Concrete\Core\Entity\Permission\IpAccessControlRange',
            // Blocks
            'Concrete\Core\Entity\Block\BlockType\BlockType',
        ]);
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        Category::add('user');
        Category::add('collection');
        AccessEntityType::add('page_owner', 'Page Owner');
        AccessEntityType::add('group', 'Group');
        PermissionCategory::add('page');
        PermissionKey::add('page', 'view_page', 'View Page', '', 0, 0);
        PermissionKey::add('page', 'view_page_versions', 'View Page Versions', '', 0, 0);
        PermissionKey::add('page', 'edit_page_contents', 'Edit Page Contents', '', 0, 0);
        PermissionKey::add('page', 'edit_page_properties', 'Edit Page Properties', '', 0, 0);

        AuthenticationType::add('concrete', 'Concrete');
        $login = SinglePage::add('/login');

        $guest = Group::add('Guest', '');

        $login->setPermissionsToManualOverride();

        $pk = PermissionKey::getByHandle('view_page');
        $pk->setPermissionObject($login);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->clearPermissionAssignment();
        $pa = Access::create($pk);
        $pa->addListItem(GroupPermissionAccessEntity::getOrCreate($guest));
        $pt->assignPermissionAccess($pa);

        $em = app(EntityManagerInterface::class);
        $category = new IpAccessControlCategory();
        $category
            ->setHandle('failed_login')
            ->setName('Failed Login Attempts')
            ->setEnabled(true)
            ->setMaxEvents(5)
            ->setTimeWindow(300)
            ->setBanDuration(600)
            ->setSiteSpecific(false)
            ->setPackage(null)
        ;
        $em->persist($category);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->originalRequest = Request::getInstance();
    }

    public function tearDown(): void
    {
        $app = app();
        if ($app->resolved('session')) {
            $app->make('session')->clear();
        }
        $app->forgetInstance(User::class);
        Request::setInstance($this->originalRequest);
        parent::tearDown();
    }

    /**
     * Logging a user in makes Request::overrideGlobals() replace the PHP superglobals:
     * let PHPUnit restore them, so that they don't leak into the other test cases.
     *
     * @backupGlobals enabled
     */
    public function testConcreteLogin(): void
    {
        $password = 'Sup3r$S3cur3#P4ss';
        $hasher = app(PasswordHasher::class);
        UserInfo::addSuperUser(
            $hasher->hashPassword($password),
            'admin@example.org'
        );

        $token = app('helper/validation/token')->generate('login_concrete');
        $request = Request::create(
            'http://www.dummyco.com/login/authenticate/concrete',
            'POST',
            [
                'uName' => 'admin',
                'uPassword' => $password,
                CSRFToken::DEFAULT_TOKEN_NAME => $token,
            ]
        );
        // Make the request variables available through the PHP defaults that
        // concrete5 controllers are using
        $request->overrideGlobals();

        // This is (yet again) just for the sake of c5's "awesome" testing
        // framework...
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $server = app(ServerInterface::class);
        $response = $server->handleRequest($request);

        static::assertEquals(302, $response->getStatusCode());
        static::assertEquals(
            'http://www.dummyco.com/path/to/server/index.php/login/login_complete',
            $response->headers->get('Location')
        );

        // Create the after redirect request
        $request = Request::create(
            'http://www.dummyco.com/login/login_complete',
            'GET',
            [],
            $response->headers->getCookies()
        );
        // Make the request variables available through the PHP defaults that
        // concrete5 controllers are using
        $request->overrideGlobals();

        // This is (yet again) just for the sake of c5's "awesome" testing
        // framework...
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $response = $server->handleRequest($request);

        static::assertEquals(302, $response->getStatusCode());
        static::assertEquals(
            'http://www.dummyco.com/path/to/server/index.php',
            $response->headers->get('Location')
        );

        // The "Clear-Site-Data" header is intentionally not sent: see the comments
        // in Concrete\Controller\SinglePage\Login::login_complete()
        static::assertNull($response->headers->get('Clear-Site-Data'));
    }
}
