<?php
namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Board\Instance\Item\Data\DataInterface;
use Concrete\Core\Board\Item\ItemProviderInterface;
use Concrete\Core\Entity\Board\InstanceItem;
use Concrete\Core\Entity\Board\Item;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Doctrine\ORM\EntityManager;

class ContentPopulator implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_CONTENT;
    }

    /**
     * @var JsonSerializer
     */
    protected $serializer;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager, JsonSerializer $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
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
            $this->logger->debug(t('Item ID %s was transformed into content data %s',
                $item->getBoardItemID(), json_encode($itemData)
            ));
            $contentObjects = $contentPopulator->createContentObjects($itemData);
            $groups[] = new ItemObjectGroup($instanceItem, $contentObjects);
        }
        return $groups;
    }

}
