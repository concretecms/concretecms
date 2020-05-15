<?php

namespace Concrete\Core\Board\Instance\Item\Populator;

use Concrete\Core\Board\Instance\Item\Data\DataInterface;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceItem;
use Concrete\Core\Entity\Board\InstanceItemBatch;
use Concrete\Core\Entity\Board\ItemCategory;
use Concrete\Core\Entity\Board\ItemTag;
use Concrete\Core\Tree\Node\Type\Topic;

defined('C5_EXECUTE') or die("Access Denied.");

abstract class AbstractPopulator implements PopulatorInterface
{

    abstract public function getDataObjects(Instance $instance, Configuration $configuration) : array;

    abstract public function getObjectRelevantDate($mixed) : int;

    abstract public function getObjectName($mixed) : ?string;

    abstract public function getObjectData($mixed) : DataInterface;

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

    public function createBoardInstanceItems(Instance $instance, InstanceItemBatch $batch, ConfiguredDataSource $configuredDataSource): array
    {
        $objects = $this->getDataObjects($instance, $configuredDataSource->getConfiguration());
        $items = [];
        foreach ($objects as $object) {
            $item = new InstanceItem();
            $item->setInstance($instance);
            $item->setDataSource($configuredDataSource);
            $item->setDateCreated($batch->getDateCreated());
            $item->setBatch($batch);
            $item->setRelevantDate($this->getObjectRelevantDate($object));
            $item->setName($this->getObjectName($object));
            $item->setData($this->getObjectData($object));
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
