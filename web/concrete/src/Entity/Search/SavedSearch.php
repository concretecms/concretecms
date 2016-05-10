<?php
namespace Concrete\Core\Entity\Search;

/**
 * @MappedSuperClass
 */
abstract class SavedSearch
{


    /** @Embedded(class = "Query") */
    protected $query = null;

    /**
     * @Column(type="string")
     */
    protected $presetName;

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
