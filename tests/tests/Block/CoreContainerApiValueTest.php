<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Block\CoreContainer\Controller as CoreContainerController;
use Concrete\Core\Area\ContainerArea;
use Concrete\Core\Block\Block;
use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Page\Container\ContainerBlockInstance;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the block that displays a container of the theme.
 *
 * @see \Concrete\Block\CoreContainer\Controller::getApiValueSchema()
 * @see \Concrete\Block\CoreContainer\Controller::serializeValueForApi()
 * @see \Concrete\Block\CoreContainer\Controller::getImportDataFromApiValue()
 */
class CoreContainerApiValueTest extends BlockApiValueTestCase
{
    /**
     * The containers of the theme created by the tests, by their handle.
     *
     * @var \Concrete\Core\Entity\Page\Container[]
     */
    private $containers = [];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            Container::class,
            Container\Instance::class,
            Container\InstanceArea::class,
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->containers = [];
    }

    public function testTheAreasAreListedOnceTheyExist(): void
    {
        $block = $this->addBlock();
        $subArea = $this->createContainerArea($block, 'Column 1');

        static::assertSame(
            [['name' => 'Column 1', 'area' => $subArea->getAreaHandle()]],
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['areas']
        );
    }

    public function testTheContainerInstanceIsKeptWhenTheContainerDoesntChange(): void
    {
        $block = $this->addBlock();
        $instanceID = $this->getInstanceID($block);

        $this->updateBlock($block, ['container' => 'container_1']);

        // the areas of the container (and so the blocks placed in them) belong to its instance
        static::assertSame($instanceID, $this->getInstanceID($this->getBlock($block->getBlockCollectionObject())));
    }

    public function testTheContainerCanBeChanged(): void
    {
        $block = $this->addBlock();
        $this->getContainer('container_2');

        $this->updateBlock($block, ['container' => 'container_2']);

        static::assertSame(
            'container_2',
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['container']
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'core_container';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        return ['containerID' => $this->getContainer('container_1')->getContainerID()];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        // the areas are created by the template of the container, when the page is displayed
        return ['container' => 'container_1', 'areas' => []];
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
     * Get one of the containers of the theme (it's created the first time it's asked for).
     */
    private function getContainer(string $handle): Container
    {
        if (!isset($this->containers[$handle])) {
            $entityManager = $this->app->make(EntityManagerInterface::class);
            $container = new Container();
            $container->setContainerHandle($handle);
            $container->setContainerName(ucfirst(str_replace('_', ' ', $handle)));
            $container->setContainerIcon('full.png');
            $entityManager->persist($container);
            $entityManager->flush();
            $this->containers[$handle] = $container;
        }

        return $this->containers[$handle];
    }

    /**
     * Get the ID of the container instance a block is bound to.
     */
    private function getInstanceID(Block $block): int
    {
        $controller = $block->getController();
        static::assertInstanceOf(CoreContainerController::class, $controller);
        $instance = $controller->getContainerInstanceObject();
        static::assertInstanceOf(Container\Instance::class, $instance);

        return (int) $instance->getContainerInstanceID();
    }

    /**
     * Create one of the areas of the container, the way displaying its template does.
     */
    private function createContainerArea(Block $block, string $name)
    {
        $controller = $block->getController();
        static::assertInstanceOf(CoreContainerController::class, $controller);
        $containerBlockInstance = $this->app->make(ContainerBlockInstance::class, [
            'block' => $block,
            'instance' => $controller->getContainerInstanceObject(),
        ]);

        return (new ContainerArea($containerBlockInstance, $name))->getSubAreaObject($block->getBlockCollectionObject());
    }
}
