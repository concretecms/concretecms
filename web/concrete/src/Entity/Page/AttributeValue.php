<?php

namespace Concrete\Core\Entity\Page;

use Concrete\Core\Entity\Attribute\AbstractAttributeValue;

/**
 * @Entity
 * @Table(
 *     name="CollectionAttributeValues",
 *     indexes={
 *      @Index(name="akID", columns={"akID"}),
 *      @Index(name="avID", columns={"avID"})
 *     }
 * )
 */
class AttributeValue extends AbstractAttributeValue
{

    /**
     * @Id
     * @Column(type="integer", options={"unsigned": true})
     **/
    protected $cID;

    /**
     * @Id
     * @Column(type="integer", options={"unsigned": true})
     **/
    protected $cvID;


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

    /**
     * @return mixed
     */
    public function getVersionID()
    {
        return $this->cvID;
    }

    /**
     * @param mixed $cvID
     */
    public function setVersionID($cvID)
    {
        $this->cvID = $cvID;
    }



}
