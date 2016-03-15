<?php
namespace Concrete\Core\Entity\Attribute\Value;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="UserAttributeValues"
 * )
 */
class UserValue extends Value
{
    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     **/
    protected $uID;

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->uID;
    }

    /**
     * @param mixed $cID
     */
    public function setUserID($uID)
    {
        $this->uID = $uID;
    }
}
