<?php
namespace Concrete\Core\Entity\Attribute\Key\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atUrlSettings")
 */
class UrlSettings extends Settings
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $akUrlPlaceholder = '';

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->akUrlPlaceholder;
    }

    /**
     * @param string $placeholder
     */
    public function setPlaceholder($placeholder)
    {
        $this->akUrlPlaceholder = $placeholder;
    }
}
