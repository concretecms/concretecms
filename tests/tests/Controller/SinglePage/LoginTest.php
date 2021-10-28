<?php
namespace Concrete\Tests\Controller\SinglePage;

use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Encryption\PasswordHasher;
use Concrete\Core\Entity\Permission\IpAccessControlCategory;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ServerInterface;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\Type as AccessEntityType;
use Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use Concrete\Core\Permission\Category as PermissionCategory;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Validation\CSRF\Token as CSRFToken;
use Concrete\Core\User\UserInfo;
use Concrete\TestHelpers\Page\PageTestCase;
use Core;
use Doctrine\ORM\EntityManagerInterface;
use Group;

class LoginTest extends PageTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        // General
        $this->tables[] = 'Config';
        // Pages
        $this->tables[] = 'PageThemeCustomStyles';
        // Files
        $this->tables[] = 'FileImageThumbnailPaths';
        // Users & permissions
        $this->tables[] = 'UserGroups';
        $this->tables[] = 'Groups';
        $this->tables[] = 'TreeTypes';
        $this->tables[] = 'TreeNodes';
        $this->tables[] = 'TreeNodePermissionAssignments';
        $this->tables[] = 'AreaPermissionAssignments';
        $this->tables[] = 'PermissionAccess';
        $this->tables[] = 'PermissionAccessEntities';
        $this->tables[] = 'PermissionAccessEntityGroups';
        $this->tables[] = 'PermissionAccessList';
        $this->tables[] = 'PermissionKeyCategories';
        $this->tables[] = 'PermissionKeys';
        $this->tables[] = 'TreeNodeTypes';
        $this->tables[] = 'Trees';
        $this->tables[] = 'TreeGroupNodes';
        // Logging in/out the users
        $this->tables[] = 'AuthenticationTypes';
        // Blocks
        $this->tables[] = 'BlockTypes';
        $this->tables[] = 'Blocks';
        // Stacks
        $this->tables[] = 'Stacks';

        // Users
        $this->metadatas[] = 'Concrete\Core\Entity\User\User';
        $this->metadatas[] = 'Concrete\Core\Entity\User\UserSignup';
        $this->metadatas[] = 'Concrete\Core\Entity\Attribute\Category';
        $this->metadatas[] = 'Concrete\Core\Entity\Attribute\Key\Key';
        $this->metadatas[] = 'Concrete\Core\Entity\Attribute\Key\UserValue';
        $this->metadatas[] = 'Concrete\Core\Entity\Attribute\Key\UserKey';
        // Permissions
        $this->metadatas[] = 'Concrete\Core\Entity\Permission\IpAccessControlCategory';
        $this->metadatas[] = 'Concrete\Core\Entity\Permission\IpAccessControlRange';
        // Blocks
        $this->metadatas[] = 'Concrete\Core\Entity\Block\BlockType\BlockType';
    }

    public static function setUpBeforeClass():void
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

        $em = Core::make(EntityManagerInterface::class);
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

    public function testConcreteLogin()
    {

        $this->markTestSkipped('LoginTest skipped because it breaks subsequent tests that are expecting users table to be empty.');

        $password = 'Sup3r$S3cur3#P4ss';
        $hasher = Core::make(PasswordHasher::class);
        $admin = UserInfo::addSuperUser(
            $hasher->hashPassword($password),
            'admin@example.org'
        );

        $token = Core::make('helper/validation/token')->generate('login_concrete');
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

        $server = Core::make(ServerInterface::class);
        $response = $server->handleRequest($request);

        $this->assertEquals($response->getStatusCode(), 302);
        $this->assertEquals(
            $response->headers->get('Location'),
            'http://www.dummyco.com/path/to/server/index.php/login/login_complete'
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

        $this->assertEquals($response->getStatusCode(), 302);
        $this->assertEquals(
            $response->headers->get('Location'),
            'http://www.dummyco.com/path/to/server/index.php'
        );

        // Ensure that the "Clear-Site-Data" header is sent
        $this->assertEquals(
            $response->headers->get('Clear-Site-Data'),
            '"cache"'
        );
    }
}
