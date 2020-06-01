<?php

namespace Concrete\Core\Board\Instance\Item\Populator;

use Concrete\Core\Board\Instance\Item\Data\DataInterface;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceItem;
use Concrete\Core\Entity\Board\InstanceItemBatch;
use Concrete\Core\Entity\Board\InstanceItemCategory;
use Concrete\Core\Entity\Board\InstanceItemTag;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Tree\Node\Type\Topic;

defined('C5_EXECUTE') or die("Access Denied.");

abstract class AbstractPopulator implements PopulatorInterface
{

    abstract public function getDataObjects(
        Instance $instance,
        ConfiguredDataSource $configuration,
        int $mode
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

    public function createBoardInstanceItems(
        Instance $instance,
        InstanceItemBatch $batch,
        ConfiguredDataSource $configuredDataSource,
        $mode = PopulatorInterface::RETRIEVE_FIRST_RUN): array
    {
        $objects = $this->getDataObjects($instance, $configuredDataSource, $mode);
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
            $item->setUniqueItemId($this->getObjectUniqueItemId($object));
            $tags = $this->getObjectTags($object);
            foreach($tags as $tag) {
                $item->getTags()->add(new InstanceItemTag($item, $tag));
            }
            $categories = $this->getObjectCategories($object);
            foreach($categories as $category) {
                $item->getCategories()->add(new InstanceItemCategory($item, $category));
            }
            $item->setRelevantThumbnail($this->getObjectRelevantThumbnail($object));
            $items[] = $item;
        }
        return $items;
    }
}
