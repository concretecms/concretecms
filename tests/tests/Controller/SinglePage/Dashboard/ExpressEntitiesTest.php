<?php

namespace Concrete\Tests\Controller\SinglePage\Dashboard;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Category as PermissionCategory;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Support\Facade\Application as ApplicationFacade;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\TestHelpers\Page\DashboardPageTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Express;

/**
 * Covers the destructive actions on the Express object dashboard.
 *
 * @covers \Concrete\Controller\SinglePage\Dashboard\System\Express\Entities
 */
class ExpressEntitiesTest extends DashboardPageTestCase
{
    protected static $pageUrl = '/dashboard/system/express/entities';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        \Concrete\Core\Tree\Node\NodeType::add('category');
        \Concrete\Core\Tree\Node\NodeType::add('express_entry_category');
        \Concrete\Core\Tree\TreeType::add('express_entry_results');
        \Concrete\Core\Tree\Node\NodeType::add('express_entry_results');

        // Install the express entry permission keys before the results tree is created, so that
        // ExpressEntryResults::add() can grant them to the admin group the way a real install does.
        PermissionCategory::add('express_tree_node');
        foreach ([
            'view_express_entries' => 'View Entries',
            'add_express_entries' => 'Add Entry',
            'edit_express_entries' => 'Edit Entry',
            'delete_express_entries' => 'Delete Entry',
        ] as $handle => $name) {
            PermissionKey::add('express_tree_node', $handle, $name, '', 0, 0);
        }

        \Concrete\Core\Tree\Type\ExpressEntryResults::add();

        Category::add('express');

        $app = ApplicationFacade::getFacadeApplication();
        $factory = $app->make('\Concrete\Core\Attribute\TypeFactory');
        $factory->add('text', 'Text');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            'Concrete\Core\Entity\Express\Entity',
            'Concrete\Core\Entity\Express\Entry',
            'Concrete\Core\Entity\Express\Association',
            'Concrete\Core\Entity\Express\Form',
            // Removing an entry cascades through its associations.
            'Concrete\Core\Entity\Express\Entry\Association',
            'Concrete\Core\Entity\Express\Entry\AssociationEntry',
            'Concrete\Core\Entity\Express\Entry\ManyAssociation',
            'Concrete\Core\Entity\Express\Entry\OneAssociation',
            'Concrete\Core\Entity\Attribute\Key\ExpressKey',
            // Attribute keys are a single-table-inheritance hierarchy: hydrating any one of them joins
            // across every sibling table, so they all have to exist.
            'Concrete\Core\Entity\Attribute\Key\SiteKey',
            'Concrete\Core\Entity\Attribute\Key\SiteTypeKey',
            'Concrete\Core\Entity\Attribute\Key\PageKey',
            'Concrete\Core\Entity\Attribute\Key\FileKey',
            'Concrete\Core\Entity\Attribute\Key\EventKey',
            'Concrete\Core\Entity\Attribute\Key\LegacyKey',
            'Concrete\Core\Entity\Attribute\Value\ExpressValue',
            'Concrete\Core\Entity\Attribute\Value\Value\Value',
            'Concrete\Core\Entity\Attribute\Value\Value\TextValue',
            // Express\Entry\Listener clears the atExpressSelectedEntries join table on removal.
            'Concrete\Core\Entity\Attribute\Value\Value\ExpressValue',
            'Concrete\Core\Entity\Attribute\Key\Settings\TextSettings',
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();

        // These tests assert that the error paths render a page rather than fataling, so the whole
        // dashboard chrome gets built. With the default 'app' setting the header polls the async
        // queue, which needs Symfony messenger tables this test has no reason to create - and whether
        // the transport reports itself countable varies with suite order. Pin it so the render is
        // deterministic either way.
        $this->app->make('config')->set('concrete.messenger.consume.method', 'worker');
    }

    /**
     * Build an entity with a couple of entries in it.
     *
     * @return \Concrete\Core\Entity\Express\Entity
     */
    protected function createEntityWithEntries()
    {
        $handle = 'project' . uniqid();
        $builder = Express::buildObject($handle, $handle . 's', 'Project');
        $builder->addAttribute('text', 'Project Name', 'project_name');
        $entity = $builder->save();

        Express::buildEntry($entity)->setProjectName('First')->save();
        Express::buildEntry($entity)->setProjectName('Second')->save();

        $this->app->make(EntityManagerInterface::class)->clear();

        return $this->app->make(EntityManagerInterface::class)
            ->getRepository(Entity::class)->findOneById($entity->getId());
    }

    /**
     * @param \Concrete\Core\Entity\Express\Entity $entity
     *
     * @return int
     */
    protected function countEntries(Entity $entity)
    {
        $em = $this->app->make(EntityManagerInterface::class);
        $em->clear();

        return (int) $em->createQueryBuilder()
            ->select('count(entry.exEntryID)')
            ->from(Entry::class, 'entry')
            ->where('entry.entity = :entity')
            ->setParameter('entity', $entity->getId())
            ->getQuery()->getSingleScalarResult();
    }

    /**
     * @param string $action
     * @param array $post
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function post($action, array $post)
    {
        $url = sprintf('http://www.dummyco.com/dashboard/system/express/entities/%s', $action);

        return $this->sendRequest(Request::create($url, 'POST', $post, $this->getCookies()));
    }

    /**
     * @param string $handle
     *
     * @return string
     */
    protected function validToken($handle)
    {
        return $this->app->make(Token::class)->generate($handle);
    }

    /**
     * The admin retains the rights the granular model grants them, so clearing entries must still work.
     */
    public function testEntriesAreClearedWithAValidTokenAndPermission()
    {
        $entity = $this->createEntityWithEntries();
        $this->assertSame(2, $this->countEntries($entity), 'Precondition: the entity has entries.');
        $this->assertNotEmpty((new Checker($entity))->canDeleteExpressEntries(), 'Precondition: admin may delete entries.');

        $response = $this->post('delete_entries', [
            'entity_id' => $entity->getId(),
            'ccm_token' => $this->validToken('clear_entries'),
        ]);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(0, $this->countEntries($entity), 'A permitted request should clear the entries.');
        $this->assertFlashMessage('success', 'All Entries were successfully cleared.');
    }

    /**
     * A forged request must not destroy the entries.
     */
    public function testEntriesSurviveAForgedToken()
    {
        $entity = $this->createEntityWithEntries();

        $response = $this->post('delete_entries', [
            'entity_id' => $entity->getId(),
            'ccm_token' => 'not-a-valid-token',
        ]);

        $this->assertSame(2, $this->countEntries($entity), 'A request with an invalid token must not clear the entries.');
        $this->assertNotSame(302, $response->getStatusCode(), 'The request must not have succeeded.');
    }

    /**
     * Rejecting the request is only half of it - the rejection has to be renderable. Returning without
     * a response falls through to the default view, which renders without the variables view() sets.
     */
    public function testForgedTokenRendersAnErrorRatherThanFataling()
    {
        $entity = $this->createEntityWithEntries();

        $response = $this->post('delete_entries', [
            'entity_id' => $entity->getId(),
            'ccm_token' => 'not-a-valid-token',
        ]);

        $this->assertLessThan(
            500,
            $response->getStatusCode(),
            'The error path must render a page, not fatal on the default view.'
        );
    }

    /**
     * The entity lookup can fail, and the guard has to cope with a null entity rather than calling
     * getEntries() on it.
     */
    public function testUnknownEntityDoesNotFatal()
    {
        $response = $this->post('delete_entries', [
            'entity_id' => '00000000-0000-0000-0000-000000000000',
            'ccm_token' => $this->validToken('clear_entries'),
        ]);

        $this->assertLessThan(500, $response->getStatusCode(), 'An unknown entity must not fatal.');
    }

    /**
     * publish() and delete() share delete_entries()' error path and had the same fall-through defect.
     */
    public function testPublishRendersAnErrorRatherThanFataling()
    {
        $entity = $this->createEntityWithEntries();

        $response = $this->post('publish', [
            'entity_id' => $entity->getId(),
            'ccm_token' => 'not-a-valid-token',
        ]);

        $this->assertLessThan(500, $response->getStatusCode());
    }

    public function testDeleteRendersAnErrorRatherThanFataling()
    {
        $entity = $this->createEntityWithEntries();

        $response = $this->post('delete', [
            'entity_id' => $entity->getId(),
            'ccm_token' => 'not-a-valid-token',
        ]);

        $this->assertLessThan(500, $response->getStatusCode());
        $this->assertNotNull(
            $this->app->make(EntityManagerInterface::class)->getRepository(Entity::class)->findOneById($entity->getId()),
            'A forged delete must not remove the entity.'
        );
    }

    /**
     * The gate the controller now applies.
     *
     * This is asserted at the permission layer rather than over HTTP because the dashboard test rig
     * can only drive requests as the super user, and a super user short-circuits every permission
     * check. What it establishes is that delete_express_entries genuinely resolves for an Express
     * entity and denies a user who has not been granted it - the controller is gated on exactly this
     * call, using the same error-collection mechanism that testEntriesSurviveAForgedToken() proves
     * prevents the deletion.
     */
    public function testDeleteExpressEntriesIsDeniedToAUserWithoutTheKey()
    {
        $entity = $this->createEntityWithEntries();

        $this->assertNotEmpty(
            (new Checker($entity))->canDeleteExpressEntries(),
            'The super user should be allowed to delete entries.'
        );

        $ui = UserInfo::add([
            'uName' => 'plain' . uniqid(),
            'uEmail' => uniqid() . '@example.com',
            'uPassword' => 'p4ssW0!rd',
        ]);
        User::loginByUserID($ui->getUserID());

        $this->assertEmpty(
            (new Checker($entity))->canDeleteExpressEntries(),
            'A user who has not been granted delete_express_entries must not be allowed to clear them.'
        );
    }
}
