<?php

namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection;

use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\EntryJsonFormatter;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\Group\GroupJsonFormatter;
use JsonSerializable;

final class TreeCollectionJsonFormatter implements JsonSerializable
{
    /**
     * @var \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionInterface
     */
    protected $collection;

    /**
     * @param \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionInterface $collection
     */
    public function __construct(TreeCollectionInterface $collection)
    {
        $this->collection = $collection;
    }

    /**
     * {@inheritdoc}
     *
     * @see \JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize()
    {
        $response = [];
        $response['displayMenu'] = $this->collection->displayMenu();
        $response['entries'] = [];
        $response['entryGroups'] = [];
        foreach ($this->collection->getEntries() as $entry) {
            $formatter = new EntryJsonFormatter($entry);
            $response['entries'][] = $formatter->jsonSerialize();
        }
        foreach ($this->collection->getEntryGroups() as $group) {
            $formatter = new GroupJsonFormatter($group);
            $response['entryGroups'][] = $formatter->jsonSerialize();
        }

        return $response;
    }
}
