<?php

namespace Concrete\Core\Board\Item\Populator;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\Item;
use Concrete\Core\Entity\Board\ItemBatch;
use Concrete\Core\Entity\Board\ItemCategory;
use Concrete\Core\Entity\Board\ItemTag;
use Concrete\Core\Tree\Node\Type\Topic;

defined('C5_EXECUTE') or die("Access Denied.");

abstract class AbstractPopulator implements PopulatorInterface
{

    abstract public function getDataObjects(Board $board, Configuration $configuration) : array;
    
    abstract public function getObjectRelevantDate($mixed) : int;

    abstract public function getObjectName($mixed) : ?string;

    /**
     * @param $mixed
     * @return string[]
     */
    abstract public function getObjectTags($mixed) : array;

    /**
     * @param $mixed
     * @return Topic[]
     */
    abstract public function getObjectCategories($mixed) : array;

    public function createBoardItems(Board $board, ItemBatch $batch, ConfiguredDataSource $configuredDataSource): array
    {
        $objects = $this->getDataObjects($board, $configuredDataSource->getConfiguration());
        $items = [];
        foreach ($objects as $object) {
            $item = new Item();
            $item->setBoard($board);
            $item->setDataSource($configuredDataSource);
            $item->setDateCreated($batch->getDateCreated());
            $item->setBatch($batch);
            $item->setRelevantDate($this->getObjectRelevantDate($object));
            $item->setName($this->getObjectName($object));
            $tags = $this->getObjectTags($object);
            foreach($tags as $tag) {
                $item->getTags()->add(new ItemTag($item, $tag));
            }
            $categories = $this->getObjectCategories($object);
            foreach($categories as $category) {
                $item->getCategories()->add(new ItemCategory($item, $category));
            }
            $items[] = $item;
        }
        return $items;
    }
}
