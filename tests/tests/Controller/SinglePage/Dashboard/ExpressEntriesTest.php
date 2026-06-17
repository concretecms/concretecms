<?php

namespace Concrete\Tests\Controller\SinglePage\Dashboard;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Application as ApplicationFacade;
use Concrete\TestHelpers\Page\DashboardPageTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Express;

class ExpressEntriesTest extends DashboardPageTestCase
{
    protected static $pageUrl = '/dashboard/express/entries';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        \Concrete\Core\Tree\Node\NodeType::add('category');
        \Concrete\Core\Tree\Node\NodeType::add('express_entry_category');
        \Concrete\Core\Tree\TreeType::add('express_entry_results');
        \Concrete\Core\Tree\Node\NodeType::add('express_entry_results');

        $tree = \Concrete\Core\Tree\Type\ExpressEntryResults::add();

        Category::add('express');

        $app = ApplicationFacade::getFacadeApplication();
        $factory = $app->make('\Concrete\Core\Attribute\TypeFactory');
        $factory->add('text', 'Text');

        $builder = Express::buildObject('project', 'projects', 'Project');
        $builder->addAttribute('text', 'Project Name', 'project_name');
        $project = $builder->save();
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
            'Concrete\Core\Entity\Attribute\Key\ExpressKey',
            'Concrete\Core\Entity\Attribute\Value\ExpressValue',
            'Concrete\Core\Entity\Attribute\Value\Value\Value',
            'Concrete\Core\Entity\Attribute\Value\Value\TextValue',
            'Concrete\Core\Entity\Attribute\Key\Settings\TextSettings',
        ]);
    }

    public function testUnexistingFormSubmitWithoutEntity(): void
    {
        $id = '00000000-0000-0000-0000-000000000000';
        $url = sprintf('http://www.dummyco.com/dashboard/express/entries/submit/%s', $id);
        $request = Request::create($url, 'GET', [], $this->getCookies());
        $response = $this->sendRequest($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(
            'http://www.dummyco.com/path/to/server/index.php/dashboard/express/entries',
            $response->headers->get('Location')
        );
        $this->assertFlashMessage('error', 'No details about the form provided.');
    }

    public function testUnexistingFormSubmitWithEntity(): void
    {
        $em = $this->app->make(EntityManagerInterface::class);
        $r = $em->getRepository(Entity::class);
        $entity = $r->findOneBy([]);

        $url = sprintf('http://www.dummyco.com/dashboard/express/entries/submit/%s', $entity->getId());
        $request = Request::create($url, 'GET', [], $this->getCookies());
        $response = $this->sendRequest($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(
            sprintf('http://www.dummyco.com/path/to/server/index.php/dashboard/express/entries/results/%s', $entity->getId()),
            $response->headers->get('Location')
        );
        $this->assertFlashMessage('error', 'No details about the form provided.');
    }

    public function testUnexistingFormSubmitWithEntry(): void
    {
        $em = $this->app->make(EntityManagerInterface::class);
        $r = $em->getRepository(Entity::class);
        $entity = $r->findOneBy([]);

        $builder = Express::buildEntry($entity)->setProjectName('Test');
        $entry = $builder->save();

        $url = sprintf('http://www.dummyco.com/dashboard/express/entries/submit/%s', $entity->getId());
        $request = Request::create($url, 'POST', ['entry_id' => $entry->getId()], $this->getCookies());
        $response = $this->sendRequest($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(
            sprintf('http://www.dummyco.com/path/to/server/index.php/dashboard/express/entries/edit_entry/%d', $entry->getId()),
            $response->headers->get('Location')
        );
        $this->assertFlashMessage('error', 'No details about the form provided.');
    }
}
