<?php
namespace Concrete\Core\Entity\Attribute\Key\Settings;

use Concrete\Core\Entity\Attribute\Value\Value\TextValue;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atTextSettings")
 */
class TextSettings extends Settings
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $akTextPlaceholder = '';

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->akTextPlaceholder;
    }

    /**
     * @param string $placeholder
     */
    public function setPlaceholder($placeholder)
    {
        $this->akTextPlaceholder = $placeholder;
    }
}
