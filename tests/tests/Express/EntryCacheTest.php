<?php

namespace Concrete\Tests\Express;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Cache\Cache;
use Concrete\Core\Cache\Adapter\DoctrineCacheDriver;
use Concrete\Core\Express\Search\ColumnSet\DefaultSet;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\DoctrineAdapter;
use Core;
use Express;

class EntryCacheTest extends ConcreteDatabaseTestCase
{
    protected $pkg;

    protected $tables = [
        'Trees',
        'TreeNodes',
        'TreeGroupNodes',
        'TreeTypes',
        'TreeNodeTypes',
        'TreeNodePermissionAssignments',
        'PermissionAccessEntities',
        'PermissionAccessEntityGroups',
        'PermissionAccessEntityTypes',
        'PermissionKeys',
        'PermissionKeyCategories',
        'Groups',
    ];

    protected $entityClassNames = [
        'Concrete\Core\Entity\Express\Entity',
        'Concrete\Core\Entity\Express\Entry',
        'Concrete\Core\Entity\Express\Entry\Association',
        'Concrete\Core\Entity\Express\Entry\AssociationEntry',
        'Concrete\Core\Entity\Attribute\Category',
        'Concrete\Core\Entity\Attribute\Value\ExpressValue',
        'Concrete\Core\Entity\Attribute\Value\Value\Value',
        'Concrete\Core\Entity\Attribute\Value\Value\TextValue',
        'Concrete\Core\Entity\Attribute\Value\Value\ExpressValue',
        'Concrete\Core\Entity\Attribute\Value\Value\AddressValue',
        'Concrete\Core\Entity\Express\Association',
        'Concrete\Core\Entity\Attribute\Type',
        'Concrete\Core\Entity\Attribute\Key\ExpressKey',
        'Concrete\Core\Entity\Attribute\Key\Key',
        'Concrete\Core\Entity\Attribute\Key\Settings\TextSettings',
        'Concrete\Core\Entity\Attribute\Key\Settings\AddressSettings',
        'Concrete\Core\Entity\Attribute\Key\Settings\TextareaSettings',
    ];

    public function setUp(): void
    {
        $config = Core::make('config');
        $config->set('concrete.cache.enabled', true);
        $config->set('concrete.cache.doctrine_dev_mode', false);

        parent::setUp();

        $this->truncateTables();

        \Concrete\Core\Tree\Node\NodeType::add('category');
        \Concrete\Core\Tree\Node\NodeType::add('express_entry_category');
        \Concrete\Core\Tree\TreeType::add('express_entry_results');
        \Concrete\Core\Tree\Node\NodeType::add('express_entry_results');

        $tree = \Concrete\Core\Tree\Type\ExpressEntryResults::add();

        Category::add('express');

        $factory = \Core::make('\Concrete\Core\Attribute\TypeFactory');
        $factory->add('text', 'Text');

        $person = Express::buildObject('person', 'people', 'Person');
        $person->addAttribute('text', 'First Name', 'person_first_name');
        $person->addAttribute('text', 'Last Name', 'person_last_name');
        $entity = $person->save();

        // Make sure the `result_column_set` column has a value
        $entity->setResultColumnSet(new DefaultSet($entity->getAttributeKeyCategory()));
        $em = Core::make(EntityManagerInterface::class);
        $em->persist($entity);
        $em->flush();

        // This is the production cache setting that is normally applied. This
        // would happen when `concrete.cache.doctrine_dev_mode` is not enabled
        // (default).
        Core::singleton('test/cache/expensive', function () {
            $cache = new \Concrete\Core\Cache\Level\ExpensiveCache();
            $cache->enable();
            return $cache;
        });
        $ormMdCache = new DoctrineCacheDriver('test/cache/expensive');
        $em = Core::make(EntityManagerInterface::class);
        $mdf = $em->getMetadataFactory();
        $mdf->setCache(new DoctrineAdapter($ormMdCache));

        // Clear the loaded metadata for it to load again with the caching
        // layer. There is no other way doing this than through ReflectionClass.
        $reflectionClass = new \ReflectionClass('Doctrine\Persistence\Mapping\AbstractClassMetadataFactory');
        $prop = $reflectionClass->getProperty('loadedMetadata');
        $prop->setAccessible(true); // needed for PHP 7
        $prop->setValue($mdf, []);
    }

    public function tearDown(): void
    {
        $config = Core::make('config');
        $config->set('concrete.cache.enabled', false);
        $config->set('concrete.cache.doctrine_dev_mode', true);

        parent::tearDown();

        // Reset to default
        Cache::disableAll();
    }

    public function testReadEntryProductionCacheEnabled(): void
    {
        Express::buildEntry('person')
            ->setPersonFirstName('Antti')
            ->setPersonLastName('Hukkanen')
            ->save();

        $entry = Express::getEntry(1);
        $this->assertEquals('Antti', $entry->getPersonFirstName());
        $this->assertEquals('Hukkanen', $entry->getPersonLastName());
    }

    public function testReadEntryProductionCacheDisabled(): void
    {
        // Ensure the temporary expensive cache is disabled
        Core::make('test/cache/expensive')->disable();

        // It should be set to the ArrayCache when this is called. If not, the
        // test below would fail. The test below also fails if the line below
        // is commented out.
        // See: https://github.com/concretecms/concretecms/issues/12422
        Cache::disableAll();

        Express::buildEntry('person')
            ->setPersonFirstName('Antti')
            ->setPersonLastName('Hukkanen')
            ->save();

        $entry = Express::getEntry(1);
        $this->assertEquals('Antti', $entry->getPersonFirstName());
        $this->assertEquals('Hukkanen', $entry->getPersonLastName());
    }
}
