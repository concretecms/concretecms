<?php
namespace Concrete\Core\Entity\Page\Relation;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperClass
 */
abstract class Relation
{
    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $mpRelationID;

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $cID;

    /**
     * @return mixed
     */
    public function getPageRelationID()
    {
        return $this->mpRelationID;
    }

    /**
     * @param mixed $mpRelationID
     */
    public function setPageRelationID($mpRelationID)
    {
        $this->mpRelationID = $mpRelationID;
    }

    /**
     * @return mixed
     */
    public function getPageID()
    {
        return $this->cID;
    }

    /**
     * @param mixed $cID
     */
    public function setPageID($cID)
    {
        $this->cID = $cID;
    }

    public function getPageObject()
    {
        return \Page::getByID($this->cID, 'ACTIVE');
    }

}
