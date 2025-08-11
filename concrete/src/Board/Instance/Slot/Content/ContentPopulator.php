<?php
namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Board\Instance\Item\Data\DataInterface;
use Concrete\Core\Board\Instance\Logger\LoggerFactory;
use Concrete\Core\Board\Item\ItemProviderInterface;
use Concrete\Core\Entity\Board\InstanceItem;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Doctrine\ORM\EntityManager;

class ContentPopulator
{
    /**
     * @var JsonSerializer
     */
    protected $serializer;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var LoggerFactory
     */
    protected $loggerFactory;

    public function __construct(EntityManager $entityManager, JsonSerializer $serializer, LoggerFactory $loggerFactory)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->loggerFactory = $loggerFactory;
    }

    /**
     * Goes through all the possible items that are going to make it into this board, and creates
     * a large pool of potential content objects for them. These will be placed into slot templates.
     *
     * @param ItemProviderInterface[] $items
     * @return ItemObjectGroup[]
     */
    public function createContentObjects($items) : array
    {
        $groups = [];
        foreach($items as $instanceItem) {
            $item = $instanceItem->getItem();
            $this->entityManager->refresh($item);
            $dataSource = $item->getDataSource();
            $dataSourceDriver = $dataSource->getDriver();
            $contentPopulator = $dataSourceDriver->getContentPopulator();
            $itemData = $item->getData();
            if (!($itemData instanceof DataInterface)) {
                $itemData = $this->serializer->denormalize($item->getData(), $contentPopulator->getDataClass(), 'json');
            }
            $logger = null;
            if ($instanceItem instanceof InstanceItem) {
                $logger = $this->loggerFactory->createFromInstance($instanceItem->getInstance());
                $logger->write(t('Item ID %s was transformed into content',
                    $item->getBoardItemID()
                ), $itemData);
            } else {
                $logger = $this->loggerFactory->createNullLogger();
            }
            $contentObjects = $contentPopulator->createContentObjects($itemData, $logger);
            $groups[] = new ItemObjectGroup($instanceItem, $contentObjects);
        }
        return $groups;
    }

}
