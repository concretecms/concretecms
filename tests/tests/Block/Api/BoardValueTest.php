<?php

declare(strict_types=1);

namespace Concrete\Tests\Block\Api;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\Instance;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the board block.
 *
 * @see \Concrete\Block\Board\Controller::getApiValueSchema()
 * @see \Concrete\Block\Board\Controller::serializeValueForApi()
 * @see \Concrete\Block\Board\Controller::getImportDataFromApiValue()
 */
class BoardValueTest extends BlockApiValueTestCase
{
    /**
     * The board instances created by the tests, by the name of their board.
     *
     * @var \Concrete\Core\Entity\Board\Instance[]
     */
    private $instances = [];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            Board::class,
            Instance::class,
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->instances = [];
    }

    public function testTheValueRefersToTheBoardInstanceInsteadOfBeingTheNameOfItsBoard(): void
    {
        $block = $this->addBlock();

        // a CIF file can only refer to the board by its name
        $blockNode = simplexml_load_string('<root />');
        $block->export($blockNode);
        static::assertSame('Blog', (string) $blockNode->block->data->board);
        // ... but a JSON value can refer to the very instance being displayed
        static::assertSame(
            ['boardInstanceID' => (string) $this->getInstance('Blog')->getBoardInstanceID()],
            $this->getApiValue($block)
        );
    }

    public function testTheDisplayedInstanceCanBeChanged(): void
    {
        $block = $this->addBlock();
        $anotherInstance = $this->getInstance('News');

        $this->updateBlock($block, ['boardInstanceID' => (string) $anotherInstance->getBoardInstanceID()]);

        static::assertSame(
            ['boardInstanceID' => (string) $anotherInstance->getBoardInstanceID()],
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'board';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        return ['boardInstanceID' => $this->getInstance('Blog')->getBoardInstanceID()];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        return ['boardInstanceID' => (string) $this->getInstance('Blog')->getBoardInstanceID()];
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
     * Get an instance of a board (both of them are created the first time they are asked for).
     */
    private function getInstance(string $boardName): Instance
    {
        if (!isset($this->instances[$boardName])) {
            $entityManager = $this->app->make(EntityManagerInterface::class);
            $board = new Board();
            $board->setBoardName($boardName);
            $entityManager->persist($board);
            $instance = new Instance();
            $instance->setBoard($board);
            $instance->setDateCreated(1787000000);
            $entityManager->persist($instance);
            $entityManager->flush();
            $this->instances[$boardName] = $instance;
        }

        return $this->instances[$boardName];
    }
}
