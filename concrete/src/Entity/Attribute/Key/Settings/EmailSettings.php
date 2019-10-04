<?php
namespace Concrete\Core\Entity\Attribute\Key\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atEmailSettings")
 */
class EmailSettings extends Settings
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $akEmailPlaceholder = '';

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->akEmailPlaceholder;
    }

    /**
     * @param string $placeholder
     */
    public function setPlaceholder($placeholder)
    {
        $this->akEmailPlaceholder = $placeholder;
    }
}
