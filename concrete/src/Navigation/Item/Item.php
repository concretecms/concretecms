<?php
namespace Concrete\Core\Navigation\Item;

class Item implements ItemInterface, \JsonSerializable
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
     * @var bool
     */
    protected $isActiveParent = false;

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
    public function __construct(string $url, string $name, bool $isActive = false, $isActiveParent = false, $children = [])
    {
        $this->url = $url;
        $this->name = $name;
        $this->isActive = $isActive;
        $this->isActiveParent = $isActiveParent;
        $this->children = $children;
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

    /**
     * @param Item[] $children
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    public function addChild(Item $item)
    {
        $this->children[] = $item;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return bool
     */
    public function isActiveParent(): bool
    {
        return $this->isActiveParent;
    }

    /**
     * @param bool $isActiveParent
     */
    public function setIsActiveParent(bool $isActiveParent): void
    {
        $this->isActiveParent = $isActiveParent;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'url' => $this->getUrl(),
            'name' => $this->getName(),
            'isActive' => $this->isActive(),
            'isActiveParent' => $this->isActiveParent(),
            'children' => $this->getChildren(),
        ];
    }

    public function __clone()
    {
        $children = $this->getChildren();
        $newChildren = [];
        foreach($children as $child) {
            $newChildren[] = clone $child;
        }
        $this->setChildren($newChildren);
    }


}
