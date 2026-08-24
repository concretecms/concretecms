<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Core\Entity\Statistics\UsageTracker\StackUsageRecord;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the block that displays a stack.
 *
 * @see \Concrete\Block\CoreStackDisplay\Controller::getApiValueSchema()
 * @see \Concrete\Block\CoreStackDisplay\Controller::serializeValueForApi()
 * @see \Concrete\Block\CoreStackDisplay\Controller::getImportDataFromApiValue()
 */
class CoreStackDisplayApiValueTest extends BlockApiValueTestCase
{
    /**
     * The stacks created by the tests, by their name.
     *
     * @var \Concrete\Core\Page\Stack\Stack[]
     */
    private $stacks = [];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'Stacks',
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            // the block is trackable: saving it records the stack it uses
            StackUsageRecord::class,
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->stacks = [];
    }

    public function testTheStackCanBeChanged(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'stID' => $this->getStack('Another stack')->getCollectionID(),
        ]);

        static::assertSame(
            (int) $this->getStack('Another stack')->getCollectionID(),
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['stID']
        );
    }

    public function testAStackThatDoesntExistIsDiscarded(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'stID' => 0x7FFFFFFF,
        ]);

        static::assertSame(0, $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['stID']);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'core_stack_display';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        return [
            'stID' => $this->getStack('Displayed stack')->getCollectionID(),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        return [
            'stID' => (int) $this->getStack('Displayed stack')->getCollectionID(),
        ];
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
     * Get a stack (it's created the first time it's asked for).
     */
    private function getStack(string $name): Stack
    {
        if (!isset($this->stacks[$name])) {
            if (!PageType::getByHandle(STACKS_PAGE_TYPE)) {
                PageType::add(['handle' => STACKS_PAGE_TYPE, 'name' => 'Stack', 'internal' => 1]);
                SinglePage::addGlobal(STACKS_PAGE_PATH);
            }
            // the tests of a class share the tables: the stack may have been created by another one
            $stack = Stack::getByName($name);
            $this->stacks[$name] = $stack instanceof Stack ? $stack : Stack::addStack($name);
        }

        return $this->stacks[$name];
    }
}
