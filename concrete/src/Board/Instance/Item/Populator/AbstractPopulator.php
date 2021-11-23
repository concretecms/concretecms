<?php

namespace Concrete\Core\Board\Instance\Item\Populator;

use Concrete\Core\Board\Instance\Item\Data\DataInterface;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\DataSource\DataSource;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceItemCategory;
use Concrete\Core\Entity\Board\InstanceItemTag;
use Concrete\Core\Entity\Board\Item;
use Concrete\Core\Entity\Board\ItemCategory;
use Concrete\Core\Entity\Board\ItemTag;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Tree\Node\Type\Topic;

defined('C5_EXECUTE') or die("Access Denied.");

abstract class AbstractPopulator implements PopulatorInterface
{

    abstract public function getDataObjects(
        Instance $instance,
        ConfiguredDataSource $configuration
    ) : array;

    abstract public function getObjectRelevantDate($mixed) : int;

    abstract public function getObjectRelevantThumbnail($mixed): ?File;

    abstract public function getObjectUniqueItemId($mixed) : ?string;

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

    protected function getPopulationDayIntervalFutureDatetime(ConfiguredDataSource $dataSource, Instance $instance)
    {
        $site = $instance->getSite();
        $timezone = $site->getTimezone();
        $datetime = new \DateTime();
        $datetime->setTimezone(new \DateTimeZone($timezone));
        $datetime->add(new \DateInterval(sprintf("P%sD", $dataSource->getPopulationDayIntervalFuture())));
        return $datetime;
    }

    protected function getPopulationDayIntervalPastDatetime(ConfiguredDataSource $dataSource, Instance $instance)
    {
        $site = $instance->getSite();
        $timezone = $site->getTimezone();
        $datetime = new \DateTime();
        $datetime->setTimezone(new \DateTimeZone($timezone));
        $datetime->sub(new \DateInterval(sprintf("P%sD", $dataSource->getPopulationDayIntervalPast())));
        return $datetime;
    }

    public function createItemsFromDataSource(
        Instance $instance,
        ConfiguredDataSource $configuredDataSource): array
    {
        $objects = $this->getDataObjects($instance, $configuredDataSource);
        $items = [];
        foreach ($objects as $object) {
            $items[] = $this->createItemFromObject($configuredDataSource->getDataSource(), $object);
        }
        return $items;
    }

    /**
     * @param $mixed
     * @return Item|null
     */
    public function createItemFromObject(DataSource $dataSource, $object): ?Item
    {
        $item = new Item();
        $item->setDateCreated(time());
        $item->setDataSource($dataSource);
        $item->setRelevantDate($this->getObjectRelevantDate($object));
        $item->setName($this->getObjectName($object));
        $item->setData($this->getObjectData($object));
        $item->setUniqueItemId($this->getObjectUniqueItemId($object));
        $tags = $this->getObjectTags($object);
        foreach($tags as $tag) {
            $item->getTags()->add(new ItemTag($item, $tag));
        }
        $categories = $this->getObjectCategories($object);
        foreach($categories as $category) {
            $item->getCategories()->add(new ItemCategory($item, $category));
        }
        $item->setRelevantThumbnail($this->getObjectRelevantThumbnail($object));
        return $item;
    }
}
