<?php
namespace Concrete\Core\Entity\Attribute\Key\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atNumberSettings")
 */
class NumberSettings extends Settings
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $akNumberPlaceholder = '';

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->akNumberPlaceholder;
    }

    /**
     * @param string $placeholder
     */
    public function setPlaceholder($placeholder)
    {
        $this->akNumberPlaceholder = $placeholder;
    }
}
