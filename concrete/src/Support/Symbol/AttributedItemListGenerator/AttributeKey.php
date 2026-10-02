<?php

declare(strict_types=1);

namespace Concrete\Core\Support\Symbol\AttributedItemListGenerator;

defined('C5_EXECUTE') or die('Access Denied.');

final class AttributeKey
{
    /**
     * @var string
     */
    private $categoryHandle;

    /**
     * @var string
     */
    private $handle;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $searchable;

    public function __construct(string $categoryHandle, string $handle, string $name = '', bool $searchable = false)
    {
        $this->categoryHandle = $categoryHandle;
        $this->handle = $handle;
        $this->name = $name;
        $this->searchable = $searchable;
    }

    public function getCategoryHandle(): string
    {
        return $this->categoryHandle;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Is the attribute key searchable (that is: does it have a column in the search index table used by the item lists)?
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }
}
