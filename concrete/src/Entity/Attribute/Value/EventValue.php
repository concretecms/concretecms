<?php
namespace Concrete\Core\Entity\Attribute\Value;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="CalendarEventVersionAttributeValues"
 * )
 */
class EventValue extends AbstractValue
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Calendar\CalendarEventVersion")
     * @ORM\JoinColumn(name="eventVersionID", referencedColumnName="eventVersionID")
     */
    protected $version;

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }


}
