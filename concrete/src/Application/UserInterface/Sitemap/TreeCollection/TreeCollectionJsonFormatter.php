<?php
namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection;

use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\EntryGroupJsonFormatter;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\EntryJsonFormatter;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\Group\GroupJsonFormatter;

final class TreeCollectionJsonFormatter implements \JsonSerializable
{

    protected $collection;

    public function __construct(TreeCollectionInterface $collection)
    {
        $this->collection = $collection;
    }


    public function jsonSerialize()
    {
        $response = array();
        $response['displayMenu'] = $this->collection->displayMenu();
        $response['entries'] = array();
        $response['entryGroups'] = array();
        foreach($this->collection->getEntries() as $entry) {
            $formatter = new EntryJsonFormatter($entry);
            $response['entries'][] = $formatter->jsonSerialize();
        }
        foreach($this->collection->getEntryGroups() as $group) {
            $formatter = new GroupJsonFormatter($group);
            $response['entryGroups'][] = $formatter->jsonSerialize();
        }
        return $response;
    }


}
