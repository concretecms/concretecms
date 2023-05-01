<?php

namespace Concrete\Core\Navigation\Item;

use Concrete\Core\Page\Page;

use Concrete\Core\Navigation\Item\Traits\SupportsChildrenItemTrait;

class PageItem implements SerializableItemInterface, SupportsChildrenItemInterface, LinkItemInterface
{

    use SupportsChildrenItemTrait;

    /**(
     * @var Page
     */
    protected $page;

    /**
     * DashboardPageItem constructor.
     * @param Page $page
     */
    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function getName(): string
    {
        return $this->page->getCollectionName();
    }

    public function getUrl(): string
    {
        return (string) $this->page->getCollectionLink();
    }

    public function getPageID(): int
    {
        return $this->page->getCollectionID();
    }

    public function getKeywords(): ?string
    {
        return $this->page->getAttribute('meta_keywords');
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'pageID' => $this->getPageID(),
            'url' => $this->getUrl(),
            'keywords' => $this->getKeywords(),
            'isActive' => $this->isActive(),
            'isActiveParent' => $this->isActiveParent(),
        ];
    }


}

