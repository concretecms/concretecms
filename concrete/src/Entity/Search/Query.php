<?php
namespace Concrete\Core\Entity\Search;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Query implements \JsonSerializable
{

    /**
     * @ORM\Column(type="object")
     */
    protected $fields = array();

    /**
     * @ORM\Column(type="object")
     */
    protected $columns;

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
        return array(
            'fields' => $this->fields,
        );

    }


}
