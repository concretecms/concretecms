<?php
namespace Concrete\Core\Entity\Search;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class SavedSearch
{


    /** @ORM\Embedded(class = "\Concrete\Core\Entity\Search\Query") */
    protected $query = null;

    /**
     * @ORM\Column(type="string")
     */
    protected $presetName;

    /**
     * The identifier of the saved search: declared (and mapped) by the subclasses.
     *
     * @var int|null
     */
    protected $id;

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPresetName()
    {
        return $this->presetName;
    }

    /**
     * @param mixed $presetName
     */
    public function setPresetName($presetName)
    {
        $this->presetName = $presetName;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }



}
