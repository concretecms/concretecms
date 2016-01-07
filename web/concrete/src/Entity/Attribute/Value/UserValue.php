<?php

namespace Concrete\Core\Entity\Attribute\Value;

/**
 * @Entity
 * @Table(
 *     name="UserAttributeValues"
 * )
 */
class UserValue extends Value
{

    /**
     * @Column(type="integer", options={"unsigned": true})
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
    public function serUserID($uID)
    {
        $this->uID = $uID;
    }





}
