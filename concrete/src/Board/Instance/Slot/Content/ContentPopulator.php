<?php
namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Board\Instance\Item\Data\DataInterface;
use Concrete\Core\Entity\Board\InstanceItem;
use Concrete\Core\Foundation\Serializer\JsonSerializer;

class ContentPopulator
{


    /**
     * @var JsonSerializer
     */
    protected $serializer;

    public function __construct(JsonSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Goes through all the possible items that are going to make it into this board, and creates
     * a large pool of potential content objects for them. These will be placed into slot templates.
     *
     * @param InstanceItem[] $items
     * @return ItemObjectGroup[]
     */
    public function createContentObjects($items) : array
    {
        $groups = [];
        foreach($items as $item) {
            $dataSourceDriver = $item->getDataSource()->getDataSource()->getDriver();
            $contentPopulator = $dataSourceDriver->getContentPopulator();
            $itemData = $item->getData();
            if (!($itemData instanceof DataInterface)) {
                $itemData = $this->serializer->denormalize($item->getData(), $contentPopulator->getDataClass(), 'json');
            }
            $contentObjects = $contentPopulator->createContentObjects($itemData);
            $groups[] = new ItemObjectGroup($item, $contentObjects);
        }
        return $groups;
    }

}
