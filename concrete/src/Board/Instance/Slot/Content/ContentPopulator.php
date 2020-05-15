<?php
namespace Concrete\Core\Board\Instance\Slot\Content;

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
     * @param Item[] $items
     * @return ObjectInterface[]
     */
    public function createContentObjects($items) : array
    {
        $groups = [];
        foreach($items as $item) {
            $dataSourceDriver = $item->getDataSource()->getDataSource()->getDriver();
            $contentPopulator = $dataSourceDriver->getContentPopulator();
            $data = $this->serializer->denormalize($item->getData(), $contentPopulator->getDataClass(), 'json');
            $contentObjects = $contentPopulator->createContentObjects($data);
            $groups[] = new ItemObjectGroup($item, $contentObjects);
        }
        return $groups;
    }

}
