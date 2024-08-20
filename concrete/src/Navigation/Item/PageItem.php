<?php

namespace Concrete\Core\Navigation\Item;

use Concrete\Core\Page\Page;

class PageItem extends Item
{

    /**
     * @var int
     */
    protected $pageID;

    /**
     * @var string
     */
    protected $keywords = '';

    /**
     * Item constructor.
     * @param string $url
     * @param string $name
     * @param bool $isActive
     */
    public function __construct(?Page $page = null, bool $isActive = false)
    {
        if ($page) {
            $this->pageID = $page->getCollectionID();
            $this->keywords = (string)$page->getAttribute("meta_keywords");
            parent::__construct($this->getURL(), $page->getCollectionName(), $isActive);
        }
        if ($this->keywords === null) {
            $this->keywords = '';
        }
    }

    /**
     * @return int
     */
    public function getPageID(): int
    {
        return $this->pageID;
    }

    /**
     * @param int $pageID
     */
    public function setPageID(int $pageID): void
    {
        $this->pageID = $pageID;
    }

    /**
     * @return string
     */
    public function getURL(): string
    {
        $p = Page::getByID($this->pageID);
        if ($p->isExternalLink()) {
            $url = $p->getCollectionPointerExternalLink();
        } else if ($p->getAttribute('replace_link_with_first_in_nav')) {
            $child = $p->getFirstChild();
            $url = $child instanceof Page ? $child->getCollectionLink() : $p->getCollectionLink();
        } else {
            $url = $p->getCollectionLink();
        }
        return $url;
    }
    
    /**
     * @return string
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     * @return PageItem
     */
    public function setKeywords(string $keywords): PageItem
    {
        $this->keywords = $keywords;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Navigation\Item\Item::getName()
     */
    public function getName(): string
    {
        return t(parent::getName());
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            // We need to use the parent's getName method (it's in English)
            'name' => parent::getName(),
            'pageID' => $this->getPageID(),
            'url' => $this->getURL(),
            'keywords' => $this->getKeywords(),
        ] + parent::jsonSerialize();
    }


}
