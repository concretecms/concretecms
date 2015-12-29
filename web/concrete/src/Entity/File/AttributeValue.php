<?php

namespace Concrete\Core\Entity\File;

use Concrete\Core\Entity\Attribute\AbstractAttributeValue;

/**
 * @Entity
 * @Table(
 *     name="FileAttributeValues",
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
    protected $fID;

    /**
     * @Id
     * @Column(type="integer", options={"unsigned": true})
     **/
    protected $fvID;


    /**
     * @return mixed
     */
    public function getFileID()
    {
        return $this->fID;
    }

    /**
     * @param mixed $fID
     */
    public function setFileID($fID)
    {
        $this->fID = $fID;
    }

    /**
     * @return mixed
     */
    public function getVersionID()
    {
        return $this->fvID;
    }

    /**
     * @param mixed $fvID
     */
    public function setVersionID($fvID)
    {
        $this->fvID = $fvID;
    }


}
