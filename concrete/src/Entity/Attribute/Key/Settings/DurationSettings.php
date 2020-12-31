<?php

namespace Concrete\Core\Entity\Attribute\Key\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atDurationSettings")
 */
class DurationSettings extends Settings
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $akUnitType = '';

    /**
     * @return string
     */
    public function getUnitType(): string
    {
        return $this->akUnitType;
    }

    /**
     * @param string $unitType
     * @return DurationSettings
     */
    public function setUnitType(string $unitType): DurationSettings
    {
        $this->akUnitType = $unitType;
        return $this;
    }

}
