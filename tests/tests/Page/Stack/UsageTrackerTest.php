<?php

namespace Concrete\Tests\Page\Stack;

use Concrete\Block\CoreStackDisplay\Controller as StackDisplayController;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Statistics\UsageTracker\StackUsageRecord;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Stack\UsageTracker;
use Concrete\Tests\TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * Regression test: UsageTracker::trackBlocks() used to decide whether a block was a stack-display
 * proxy purely by comparing Block::getBlockTypeHandle() to BLOCK_HANDLE_STACK_PROXY, and then
 * unconditionally called getStackID() on whatever Block::getController() resolved. If the Block
 * object was stale relative to what getController() currently resolves (as can happen when a Block
 * instance is reused across several block types on a shared/cached collection, such as the page
 * reused across many CIF import cases in ImportExportTest), getBlockTypeHandle() could report
 * BLOCK_HANDLE_STACK_PROXY while getController() correctly resolved to a completely unrelated
 * controller (e.g. File, HeroImage), which has no getStackID() method. This produced errors such
 * as:
 *   Call to undefined method Concrete\Block\File\Controller::getStackID()
 *   Call to undefined method Concrete\Block\HeroImage\Controller::getStackID()
 *
 * trackBlocks() must instead trust the actual class of the resolved controller before calling a
 * stack-only method on it.
 */
class UsageTrackerTest extends TestCase
{
    public function testTrackBlocksDoesNotCallGetStackIdOnUnrelatedController(): void
    {
        // A block that misreports the stack-proxy handle (simulating the stale-block scenario),
        // but whose resolved controller is unrelated to stacks and has no getStackID() method.
        $unrelatedController = $this->getMockBuilder(BlockController::class)
            ->disableOriginalConstructor()
            ->getMock();

        $unrelatedBlock = $this->getMockBuilder(Block::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getBlockTypeHandle', 'getController', 'getBlockID'])
            ->getMock();
        $unrelatedBlock->method('getBlockTypeHandle')->willReturn(BLOCK_HANDLE_STACK_PROXY);
        $unrelatedBlock->method('getController')->willReturn($unrelatedController);
        $unrelatedBlock->method('getBlockID')->willReturn(101);

        // A genuine stack-display block/controller, which should still be tracked normally.
        $stackController = $this->getMockBuilder(StackDisplayController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStackID'])
            ->getMock();
        $stackController->expects($this->once())->method('getStackID')->willReturn(42);

        $stackBlock = $this->getMockBuilder(Block::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getBlockTypeHandle', 'getController', 'getBlockID'])
            ->getMock();
        $stackBlock->method('getBlockTypeHandle')->willReturn(BLOCK_HANDLE_STACK_PROXY);
        $stackBlock->method('getController')->willReturn($stackController);
        $stackBlock->method('getBlockID')->willReturn(102);

        $collection = $this->getMockBuilder(Collection::class)->getMock();
        $collection->method('getBlocks')->willReturn([$unrelatedBlock, $stackBlock]);
        $collection->method('getVersionID')->willReturn(1);
        $collection->method('getCollectionID')->willReturn(5);

        $repository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository->method('findOneBy')->willReturn(null);

        $manager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $manager->method('getRepository')->with(StackUsageRecord::class)->willReturn($repository);
        // Before the fix, calling track() would throw a fatal error while iterating $unrelatedBlock,
        // before ever reaching merge() for the genuine stack block.
        $manager->expects($this->once())->method('merge');

        $tracker = new UsageTracker($manager);

        // Before the fix, this would throw:
        // Error: Call to undefined method ...Controller::getStackID()
        $tracker->track($collection);
    }
}