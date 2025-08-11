<?php

namespace Concrete\Core\Navigation\Item;

use Concrete\Core\Entity\Search\SavedSearch;

/**
 * @method SavedSearchItem[] getChildren()
 */
class SavedSearchItem extends Item
{
    /**
     * @var int
     */
    protected $id;

    /**
     * Item constructor.
     *
     * @param SavedSearch $savedSearch
     */
    public function __construct(SavedSearch $savedSearch)
    {
        parent::__construct('', $savedSearch->getPresetName());

        $this->id = $savedSearch->getID();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        $data['id'] = $this->getId();
        $data['type'] = 'saved_search';

        return $data;
    }
}
