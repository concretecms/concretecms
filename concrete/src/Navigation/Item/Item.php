<?php
namespace Concrete\Core\Navigation\Item;

class Item implements ItemInterface
{

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $isActive;

    /**
     * @var Item[]
     */
    protected $children = [];

    /**
     * Item constructor.
     * @param string $url
     * @param string $name
     * @param bool $isActive
     */
    public function __construct(string $url, string $name, bool $isActive = false)
    {
        $this->url = $url;
        $this->name = $name;
        $this->isActive = $isActive;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return Item[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function addChild(Item $item)
    {
        $this->children[] = $item;
    }


}
