<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\Access\EntryInterface;

/**
 * @since 8.0.0
 */
abstract class AbstractAssignment implements AssignmentInterface
{

    protected $accessEntry;
    protected $registry;

    public function getEntry()
    {
        return $this->accessEntry;
    }

    public function setEntry($accessEntry)
    {
        $this->accessEntry = $accessEntry;
    }

    /**
     * @return mixed
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * @param mixed $registry
     */
    public function setRegistry($registry)
    {
        $this->registry = $registry;
    }




}