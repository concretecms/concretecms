<?php
namespace Concrete\Core\Entity\Attribute\Key\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atTelephoneSettings")
 */
class TelephoneSettings extends Settings
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $akTelephonePlaceholder = '';

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->akTelephonePlaceholder;
    }

    /**
     * @param string $placeholder
     */
    public function setPlaceholder($placeholder)
    {
        $this->akTelephonePlaceholder = $placeholder;
    }
}
