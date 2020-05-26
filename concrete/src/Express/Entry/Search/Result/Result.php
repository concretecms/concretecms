<?php
namespace Concrete\Core\Express\Entry\Search\Result;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Search\Column\Column as BaseColumn;
use Concrete\Core\Search\Result\Result as SearchResult;

class Result extends SearchResult
{

    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @param Entity $entity
     */
    public function setEntity(Entity $entity): void
    {
        $this->entity = $entity;
    }

    /**
     * @return Entity
     */
    public function getEntity(): Entity
    {
        return $this->entity;
    }

    public function getItemDetails($item)
    {
        $node = new Item($this, $this->listColumns, $item);

        return $node;
    }
}
