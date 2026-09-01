<?php

declare(strict_types=1);

namespace Concrete\Tests\Api\Block;

use Concrete\Core\Api\Fractal\Transformer\BlockTypeTransformer;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Cache\CacheLocal;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\Core\Entity\Package as PackageEntity;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests what the API tells about a block type.
 *
 * @see \Concrete\Core\Api\Fractal\Transformer\BlockTypeTransformer
 */
class BlockTypeTransformerTest extends ConcreteDatabaseTestCase
{
    protected $tables = [
        'BlockTypeSets',
    ];

    protected $entityClassNames = [
        BlockTypeEntity::class,
        PackageEntity::class,
    ];

    public function testTheBlockTypesOfTheCoreBelongToNoPackage(): void
    {
        if (BlockType::getByHandle('content') === null) {
            BlockType::installBlockType('content');
        }
        // fetch it again: installBlockType() doesn't load the controller
        $blockType = BlockType::getByHandle('content');

        $value = $this->transform($blockType);

        static::assertSame('content', $value['handle']);
        static::assertNull($value['package']);
        static::assertArrayHasKey('value_schema', $value);
    }

    public function testTheBlockTypesOfAPackageTellWhichOne(): void
    {
        $blockType = $this->createPackageBlockType('the_block_of_the_package', 'my_package');

        $value = $this->transform($blockType);

        static::assertSame('the_block_of_the_package', $value['handle']);
        static::assertSame('my_package', $value['package']);
    }

    /**
     * Only the block types of the core belong to no package: the handle of a package is whatever it is.
     */
    public function testAPackageHandleThatLooksLikeNothingIsStillAPackage(): void
    {
        $blockType = $this->createPackageBlockType('the_block_of_the_other_package', '0');

        static::assertSame('0', $this->transform($blockType)['package']);
    }

    /**
     * Get what the API tells about a block type.
     *
     * @return array<string,mixed>
     */
    private function transform(BlockTypeEntity $blockType): array
    {
        return app(BlockTypeTransformer::class)->transform($blockType);
    }

    /**
     * Get a block type provided by a package that the site has.
     */
    private function createPackageBlockType(string $handle, string $packageHandle): BlockTypeEntity
    {
        $blockType = new class extends BlockTypeEntity {
            /**
             * {@inheritdoc}
             *
             * @see \Concrete\Core\Entity\Block\BlockType\BlockType::getController()
             */
            public function getController()
            {
                return new class extends BlockController {
                };
            }
        };
        $blockType->setBlockTypeHandle($handle);
        $blockType->setBlockTypeName('The block of the package');
        $blockType->setPackageID($this->createPackage($packageHandle));

        return $blockType;
    }

    /**
     * Add a package to the site.
     *
     * @return int the ID of the package
     */
    private function createPackage(string $handle): int
    {
        $entityManager = app(EntityManagerInterface::class);
        $package = new PackageEntity();
        $package->setPackageHandle($handle);
        $package->setPackageName('My package');
        $package->setPackageDescription('The package of the tests');
        $package->setPackageVersion('1.0.0');
        $package->setPackageDateInstalled(new \DateTime());
        $entityManager->persist($package);
        $entityManager->flush();
        // the handles of the packages are kept in memory once they are asked for
        CacheLocal::flush();

        return (int) $package->getPackageID();
    }
}
