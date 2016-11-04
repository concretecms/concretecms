<?php
namespace Concrete\Core\Entity\Attribute\Value;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="CollectionAttributeValues"
 * )
 */
class PageValue extends AbstractValue
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned": true})
     **/
    protected $cID;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned": true})
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
