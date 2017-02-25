<?php
namespace Concrete\Core\Entity\Search;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Query implements \JsonSerializable
{
    const MAX_ITEMS_PER_PAGE = 10;

    /**
     * @ORM\Column(type="object")
     */
    protected $fields = [];

    /**
     * @ORM\Column(type="object")
     */
    protected $columns;

    /**
     * @ORM\Column(type="smallint")
     */
    private $itemsPerPage;

    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param mixed $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return mixed
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param mixed $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    public function jsonSerialize()
    {
        return [
            'fields' => $this->fields,
        ];
    }

    /**
     * @param int
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = is_numeric($itemsPerPage) ? $itemsPerPage : self::MAX_ITEMS_PER_PAGE;
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }
}
