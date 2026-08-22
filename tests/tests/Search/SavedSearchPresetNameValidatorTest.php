<?php

declare(strict_types=1);

namespace Concrete\Tests\Search;

use Concrete\Core\Entity\Express\Entity as ExpressEntity;
use Concrete\Core\Entity\Search\SavedExpressSearch;
use Concrete\Core\Entity\Search\SavedUserSearch;
use Concrete\Core\Search\SavedSearchPresetNameValidator;
use Concrete\Tests\TestCase;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mockery as M;

defined('C5_EXECUTE') or die('Access Denied.');

class SavedSearchPresetNameValidatorTest extends TestCase
{
    public function testNewRegularPresetNameIsUniqueWhenNoMatchesExist()
    {
        $search = new SavedUserSearch();
        $validator = $this->createValidator(
            SavedUserSearch::class,
            ['presetName' => 'Example'],
            []
        );

        static::assertTrue($validator->isUnique($search, 'Example'));
    }

    public function testNewRegularPresetNameIsNotUniqueWhenAMatchExists()
    {
        $search = new SavedUserSearch();
        $existingSearch = new SavedUserSearch();
        self::setNonPublicPropertyValue($existingSearch, 'id', 1);
        $validator = $this->createValidator(
            SavedUserSearch::class,
            ['presetName' => 'Example'],
            [$existingSearch]
        );

        static::assertFalse($validator->isUnique($search, 'Example'));
    }

    public function testExistingPresetDoesNotConflictWithItself()
    {
        $search = new SavedUserSearch();
        self::setNonPublicPropertyValue($search, 'id', 1);
        $validator = $this->createValidator(
            SavedUserSearch::class,
            ['presetName' => 'Example'],
            [$search]
        );

        static::assertTrue($validator->isUnique($search, 'Example'));
    }

    public function testExistingPresetConflictsWithAnotherMatchingPreset()
    {
        $search = new SavedUserSearch();
        self::setNonPublicPropertyValue($search, 'id', 1);
        $existingSearch = new SavedUserSearch();
        self::setNonPublicPropertyValue($existingSearch, 'id', 2);
        $validator = $this->createValidator(
            SavedUserSearch::class,
            ['presetName' => 'Example'],
            [$search, $existingSearch]
        );

        static::assertFalse($validator->isUnique($search, 'Example'));
    }

    public function testExpressPresetNamesAreScopedByEntity()
    {
        $entity = M::mock(ExpressEntity::class);
        $search = new SavedExpressSearch();
        $search->setEntity($entity);
        $validator = $this->createValidator(
            SavedExpressSearch::class,
            ['presetName' => 'Example', 'entity' => $entity],
            []
        );

        static::assertTrue($validator->isUnique($search, 'Example'));
    }

    private function createValidator(string $className, array $criteria, array $matches): SavedSearchPresetNameValidator
    {
        $repository = M::mock(ObjectRepository::class);
        $repository->shouldReceive('findBy')->once()->with($criteria)->andReturn($matches);

        $entityManager = M::mock(EntityManagerInterface::class);
        $entityManager->shouldReceive('getRepository')->once()->with($className)->andReturn($repository);

        return new SavedSearchPresetNameValidator($entityManager);
    }
}
