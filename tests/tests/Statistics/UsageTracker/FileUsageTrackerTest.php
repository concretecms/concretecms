<?php

namespace Concrete\Tests\Statistics\UsageTracker;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRepository;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\File\Tracker\UsageTracker;
use Concrete\Core\Page\Collection\Collection;
use Concrete\TestHelpers\Statistics\UsageTracker\TrackableBlockController;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit_Framework_TestCase;

class FileUsageTrackerTest extends PHPUnit_Framework_TestCase
{
    public function testTrackingCollection()
    {
        $trackable = $this->getFileTrackableMock([], 1, 5);
        $collection = $trackable->getUsedCollection();

        $nonTrackableBuilder = $this->getMockBuilder(BlockController::class);
        $nonTrackableBuilder->disableProxyingToOriginalMethods();
        $nonTrackableBuilder->disableOriginalConstructor();
        $nonTrackableBlock = $nonTrackableBuilder->getMock();
        $nonTrackableBlock->method('getCollectionObject')->willReturn($collection);

        $builder = $this->getMockBuilder(TrackableBlockController::class);
        $builder->disableProxyingToOriginalMethods();
        $builder->disableOriginalConstructor();
        $builder->setMethods([
            'getCollectionObject',
            'getBlockObject',
            'getUsedCollection',
            'getUsedFiles',
        ]);
        $trackableBlock = $builder->getMockForAbstractClass();

        $trackableBlock->method('getUsedCollection')->willReturn($collection);
        $trackableBlock->method('getUsedFiles')->willReturn([10, 5]);

        $blockBuilder = $this->getMockBuilder(Block::class);
        $blockBuilder->disableProxyingToOriginalMethods();

        $blockBuilder->disableOriginalConstructor();

        $block1 = $blockBuilder->getMock();
        $block1->method('getController')->willReturn($nonTrackableBlock);
        $nonTrackableBlock->method('getBlockObject')->willReturn($block1);

        $block2 = $blockBuilder->getMock();
        $block2->method('getBlockID')->willReturn(55);
        $block2->method('getController')->willReturn($trackableBlock);
        $trackableBlock->method('getBlockObject')->willReturn($block2);

        $collection->method('getBlocks')->willReturn([$block1, $block2]);
        $collection->method('getSetCollectionAttributes')->willReturn([]);

        $items = [];
        $manager = $this->getDatabaseMock();
        $manager->expects($this->exactly(2))->method('merge')->willReturnCallback(function ($item) use (&$items) {
            $items[] = $item;
        });

        $trackableBlock->method('getUsedFiles')->willReturn([10]);
        $trackableBlock->method('getBlockObject')->willReturn([10]);

        // Test the tracker
        $tracker = new UsageTracker($manager);
        $tracker->track($collection);

        $item = array_shift($items);
        $this->assertEquals([10, 1, 5, 55], [
            $item->getFileID(),
            $item->getCollectionID(),
            $item->getCollectionVersionID(),
            $item->getBlockID(), ]);

        $item = array_shift($items);
        $this->assertEquals([5, 1, 5, 55], [
            $item->getFileID(),
            $item->getCollectionID(),
            $item->getCollectionVersionID(),
            $item->getBlockID(), ]);
    }

    public function testTrackingBlockController()
    {
        $trackable = $this->getFileTrackableMock([], 10, 5);
        $collection = $trackable->getUsedCollection();

        $blockClass = $this->getMockClass(Block::class, [
            'getBlockID',
            'getController',
        ]);
        $block = new $blockClass();
        $block->method('getBlockID')->willReturn(105);

        $controller = $this->getMockForAbstractClass(TrackableBlockController::class, [], '', false, true, true, [
            'getBlockObject',
            'getCollectionObject',
        ]);
        $controller->method('getBlockObject')->willReturn($block);
        $controller->method('getUsedFiles')->willReturn([1234, 4321]);
        $controller->method('getUsedCollection')->willReturn($collection);
        $controller->method('getCollectionObject')->willReturn($collection);

        $block->method('getController')->willReturn($controller);

        $manager = $this->getDatabaseMock();

        $items = [];
        $manager->expects($this->exactly(2))->method('merge')->willReturnCallback(function ($item) use (&$items) {
            $items[] = $item;
        });

        // Test the tracker
        $tracker = new UsageTracker($manager);
        $tracker->track($controller);

        $item = array_shift($items);
        $this->assertEquals([1234, 10, 5, 105], [
            $item->getFileID(),
            $item->getCollectionID(),
            $item->getCollectionVersionID(),
            $item->getBlockID(), ]);

        $item = array_shift($items);
        $this->assertEquals([4321, 10, 5, 105], [
            $item->getFileID(),
            $item->getCollectionID(),
            $item->getCollectionVersionID(),
            $item->getBlockID(), ]);
    }

    public function testTrackingFileTrackable()
    {
        // Prepare the mock objects
        $trackableItems = [1, 2, 5, 10, 11, 12, 13];
        $trackable = $this->getFileTrackableMock($trackableItems, 5, 9);
        $manager = $this->getDatabaseMock();
        $items = [];

        $manager->expects($this->exactly(7))->method('merge')->willReturnCallback(function ($item) use (&$items) {
            $items[] = $item;
        });

        $manager->expects($this->exactly(3))->method('flush');

        // Test the tracker
        $tracker = new UsageTracker($manager);
        $tracker->track($trackable);

        // Verify the items
        $trackedItems = [];
        foreach ($items as $item) {
            $trackedItems[] = $item->getFileID();
        }

        $this->assertEquals($trackableItems, $trackedItems);
    }

    /**
     * @param $usedFiles
     * @param $collectionID
     * @param $collectionVersionID
     *
     * @return FileTrackableInterface
     */
    private function getFileTrackableMock($usedFiles, $collectionID, $collectionVersionID)
    {
        $collectionClass = $this->getMockClass(Collection::class);
        $collection = new $collectionClass();
        $collection->method('getCollectionID')->willReturn($collectionID);
        $collection->method('getVersionID')->willReturn($collectionVersionID);

        $trackable = $this->getMockForAbstractClass(FileTrackableInterface::class);
        $trackable->method('getUsedFiles')->willReturn($usedFiles);
        $trackable->method('getUsedCollection')->willReturn($collection);

        return $trackable;
    }

    private function getDatabaseMock()
    {
        $builder = $this->getMockBuilder(FileUsageRepository::class);
        $builder->disableOriginalConstructor();
        $builder->disableProxyingToOriginalMethods();
        $repository = $builder->getMock();

        $manager = $this->getMockForAbstractClass(EntityManagerInterface::class);
        $manager->method('getRepository')->willReturn($repository);

        return $manager;
    }
}
