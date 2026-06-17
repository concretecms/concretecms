<?php

declare(strict_types=1);

namespace Concrete\Tests\Page\Type\Composer\Control\Type;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\SetManagerInterface;
use Concrete\Core\Entity\Attribute\Category as CategoryEntity;
use Concrete\Core\Entity\Attribute\Key\Key as KeyEntity;
use Concrete\Core\Entity\Attribute\Set as AttributeSet;
use Concrete\Core\Page\Type\Composer\Control\Type\CollectionAttributeType;
use Concrete\Tests\TestCase;
use Mockery as M;

defined('C5_EXECUTE') or die('Access Denied.');

class CollectionAttributeTypeTest extends TestCase
{
    // --- helpers ---

    private function makeKey(int $id): KeyEntity
    {
        $iconFormatter = M::mock();
        $keyController = M::mock();
        $keyController->shouldReceive('getIconFormatter')->andReturn($iconFormatter);

        $key = M::mock(KeyEntity::class);
        $key->shouldReceive('getAttributeKeyID')->andReturn($id);
        $key->shouldReceive('getController')->andReturn($keyController);
        $key->shouldReceive('getAttributeKeyDisplayName')->andReturn('Key ' . $id);

        return $key;
    }

    private function makeSet(array $keys): AttributeSet
    {
        $set = M::mock(AttributeSet::class);
        $set->shouldReceive('getAttributeKeys')->andReturn($keys);

        return $set;
    }

    private function makeType(array $whitelistKeys, array $sets, array $unassignedKeys): CollectionAttributeType
    {
        $setManager = M::mock(SetManagerInterface::class);
        $setManager->shouldReceive('getAttributeSets')->andReturn($sets);
        $setManager->shouldReceive('getUnassignedAttributeKeys')->andReturn($unassignedKeys);

        $categoryController = M::mock();
        $categoryController->shouldReceive('getList')->andReturn($whitelistKeys);
        $categoryController->shouldReceive('getSetManager')->andReturn($setManager);

        $category = M::mock(CategoryEntity::class);
        $category->shouldReceive('getController')->andReturn($categoryController);

        $categoryService = M::mock(CategoryService::class);
        $categoryService->shouldReceive('getByHandle')->with('collection')->andReturn($category);

        return new CollectionAttributeType($categoryService);
    }

    // --- tests ---

    public function testInternalKeyInSetIsFiltered(): void
    {
        $normalKey = $this->makeKey(1);
        $internalKey = $this->makeKey(2); // not in whitelist → internal

        $type = $this->makeType(
            [$normalKey],                          // whitelist: only key 1
            [$this->makeSet([$normalKey, $internalKey])], // set has both
            []
        );

        $result = $type->getPageTypeComposerControlObjectsBySet();

        static::assertCount(1, $result['sets']);
        static::assertCount(1, $result['sets'][0]['controls']);
        static::assertSame(1, $result['sets'][0]['controls'][0]->getAttributeKeyID());
    }

    public function testSetWhoseKeysAreAllInternalIsOmitted(): void
    {
        $internalKey = $this->makeKey(2); // not in whitelist → internal

        $type = $this->makeType(
            [],                                     // whitelist: empty
            [$this->makeSet([$internalKey])],       // set has only internal key
            []
        );

        $result = $type->getPageTypeComposerControlObjectsBySet();

        static::assertCount(0, $result['sets']);
        static::assertCount(0, $result['unassigned']);
    }

    public function testUnassignedKeysAppearInUnassigned(): void
    {
        $key = $this->makeKey(3);

        $type = $this->makeType([$key], [], [$key]);

        $result = $type->getPageTypeComposerControlObjectsBySet();

        static::assertCount(0, $result['sets']);
        static::assertCount(1, $result['unassigned']);
        static::assertSame(3, $result['unassigned'][0]->getAttributeKeyID());
    }

    public function testSetOrderingFollowsSetManagerOrder(): void
    {
        $key1 = $this->makeKey(1);
        $key2 = $this->makeKey(2);
        $setA = $this->makeSet([$key1]);
        $setB = $this->makeSet([$key2]);

        // setA before setB in the manager's ordering
        $type = $this->makeType([$key1, $key2], [$setA, $setB], []);

        $result = $type->getPageTypeComposerControlObjectsBySet();

        static::assertCount(2, $result['sets']);
        static::assertSame($setA, $result['sets'][0]['set']);
        static::assertSame($setB, $result['sets'][1]['set']);
    }

    public function testMixedSetSomeInternalKeysPreservesNonInternal(): void
    {
        $key1 = $this->makeKey(1);
        $key2 = $this->makeKey(2);
        $internalKey = $this->makeKey(99);

        // Two sets: first has a normal + internal key, second is all-internal
        $setWithMixed = $this->makeSet([$key1, $internalKey]);
        $setAllInternal = $this->makeSet([$internalKey]);

        $type = $this->makeType([$key1, $key2], [$setWithMixed, $setAllInternal], [$key2]);

        $result = $type->getPageTypeComposerControlObjectsBySet();

        static::assertCount(1, $result['sets'], 'All-internal set should be omitted');
        static::assertCount(1, $result['sets'][0]['controls'], 'Only the non-internal key should remain');
        static::assertSame(1, $result['sets'][0]['controls'][0]->getAttributeKeyID());
        static::assertCount(1, $result['unassigned']);
        static::assertSame(2, $result['unassigned'][0]->getAttributeKeyID());
    }
}
