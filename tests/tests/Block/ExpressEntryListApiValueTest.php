<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Core\Attribute\Category\CategoryService as AttributeCategoryService;
use Concrete\Core\Entity\Attribute\Category as AttributeCategory;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Attribute\Type as AttributeType;
use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Express\ObjectAssociationBuilder;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the block that lists the entries of an Express entity.
 *
 * @see \Concrete\Block\ExpressEntryList\Controller::getApiValueSchema()
 * @see \Concrete\Block\ExpressEntryList\Controller::serializeValueForApi()
 * @see \Concrete\Block\ExpressEntryList\Controller::getImportDataFromApiValue()
 */
class ExpressEntryListApiValueTest extends BlockApiValueTestCase
{
    /**
     * The Express entity whose entries are listed (and the one it's associated to).
     *
     * @var \Concrete\Core\Entity\Express\Entity[]
     */
    private $entities = [];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            Association::class,
            AttributeCategory::class,
            AttributeType::class,
            Entity::class,
            Entry::class,
            ExpressKey::class,
            Form::class,
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->entities = [];
    }

    public function testTheColumnsAreExchangedAsAList(): void
    {
        $block = $this->addBlock();
        $columns = [
            ['key' => 'e.exEntryDateCreated', 'sortDirection' => 'desc'],
            ['key' => 'e.exEntryDateModified', 'sortDirection' => 'asc'],
        ];

        $this->updateBlock($block, [
            'columns' => $columns,
            'defaultSortColumn' => ['key' => 'e.exEntryDateCreated', 'direction' => 'desc'],
        ]);

        $value = $this->getApiValue($this->getBlock($block->getBlockCollectionObject()));
        static::assertSame($columns, $value['columns']);
        static::assertSame(['key' => 'e.exEntryDateCreated', 'direction' => 'desc'], $value['defaultSortColumn']);
    }

    public function testTheFiltersKeepTheirConfiguration(): void
    {
        $block = $this->addBlock();
        $filterFields = [['key' => 'keywords', 'data' => ['keywords' => 'searchbyme']]];

        $this->updateBlock($block, [
            'filterFields' => $filterFields,
        ]);

        static::assertSame(
            $filterFields,
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['filterFields']
        );
    }

    public function testTheAssociationsAreExchangedByTheirID(): void
    {
        $block = $this->addBlock();

        static::assertSame(
            [$this->getAssociationID()],
            $this->getApiValue($block)['searchAssociations']
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'express_entry_list';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        // that's what the form of the block sends (the columns and the filters are built out of the request)
        return [
            'exEntityID' => $this->getEntity('listed')->getID(),
            'detailPage' => 0,
            'linkedProperties' => [],
            'displayLimit' => 25,
            'enablePagination' => 1,
            'enableItemsPerPageSelection' => 1,
            'enableSearch' => 1,
            'enableKeywordSearch' => 1,
            'searchProperties' => [],
            'searchAssociations' => [$this->getAssociationID()],
            'titleFormat' => 'h3',
            'tableName' => 'Our entries',
            'tableDescription' => 'The entries we share',
            'tableStriped' => 1,
            'rowBackgroundColorAlternate' => 'rgb(255, 217, 102)',
            'headerBackgroundColor' => 'rgb(255, 0, 0)',
            'headerBackgroundColorActiveSort' => 'rgb(4, 244, 50)',
            'headerTextColor' => 'rgb(41, 134, 204)',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        // the keys are in the order of the columns of the btExpressEntryList table, with the structured
        // ones at the end
        return [
            'exEntityID' => (string) $this->getEntity('listed')->getID(),
            'detailPage' => '0',
            'displayLimit' => '25',
            'enableItemsPerPageSelection' => '1',
            'enablePagination' => '1',
            'enableSearch' => '1',
            'enableKeywordSearch' => '1',
            'headerBackgroundColor' => 'rgb(255, 0, 0)',
            'headerBackgroundColorActiveSort' => 'rgb(4, 244, 50)',
            'headerTextColor' => 'rgb(41, 134, 204)',
            'tableName' => 'Our entries',
            'tableDescription' => 'The entries we share',
            'tableStriped' => '1',
            'rowBackgroundColorAlternate' => 'rgb(255, 217, 102)',
            'titleFormat' => 'h3',
            'linkedProperties' => [],
            'searchProperties' => [],
            'searchAssociations' => [$this->getAssociationID()],
            // the columns and the filters are built out of the request sending the form of the block
            'columns' => [],
            'defaultSortColumn' => null,
            'filterFields' => [],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getPartialApiValue()
     */
    protected function getPartialApiValue(): array
    {
        return ['tableName' => 'Other entries'];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::hasCustomApiValue()
     */
    protected function hasCustomApiValue(): bool
    {
        return true;
    }

    /**
     * Get an Express entity (the two of them are created the first time one is asked for).
     */
    private function getEntity(string $key): Entity
    {
        if ($this->entities === []) {
            $categoryService = $this->app->make(AttributeCategoryService::class);
            if ($categoryService->getByHandle('express') === null) {
                // the Express entities hold the attributes of their entries
                $categoryService->add('express');
            }
            $entityManager = $this->app->make(EntityManagerInterface::class);
            $repository = $entityManager->getRepository(Entity::class);
            // the tests of a class share the tables: the entities may have been created by another one
            $listed = $repository->findOneBy(['handle' => 'listed_entity']);
            $associated = $repository->findOneBy(['handle' => 'associated_entity']);
            if ($listed === null || $associated === null) {
                $listed = $this->createEntity('Listed Entity', 'listed_entity', 'listed_entities');
                $associated = $this->createEntity('Associated Entity', 'associated_entity', 'associated_entities');
                $this->app->make(ObjectAssociationBuilder::class)->addOneToMany($listed, $associated);
                $entityManager->flush();
            }
            $this->entities = ['listed' => $listed, 'associated' => $associated];
        }

        return $this->entities[$key];
    }

    /**
     * Create an Express entity.
     */
    private function createEntity(string $name, string $handle, string $pluralHandle): Entity
    {
        $entity = new Entity();
        $entity->setName($name);
        $entity->setHandle($handle);
        $entity->setPluralHandle($pluralHandle);
        $entity->setEntityResultsNodeId(0);
        $this->app->make(EntityManagerInterface::class)->persist($entity);

        return $entity;
    }

    /**
     * Get the ID of the association between the two Express entities.
     */
    private function getAssociationID(): string
    {
        return (string) $this->getEntity('listed')->getAssociations()->first()->getId();
    }
}
