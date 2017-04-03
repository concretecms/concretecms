<?php
namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry;

final class EntryJsonFormatter implements \JsonSerializable
{

    protected $entry;

    public function __construct(EntryInterface $entry)
    {
        $this->entry = $entry;
    }

    public function jsonSerialize()
    {
        $response = array(
            'element' => (string) $this->entry->getOptionElement(),
            'siteTreeID' => $this->entry->getSiteTreeID()
        );
        return $response;
    }


}
