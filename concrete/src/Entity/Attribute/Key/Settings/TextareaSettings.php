<?php
namespace Concrete\Core\Entity\Attribute\Key\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atTextareaSettings")
 */
class TextareaSettings extends Settings
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $akTextareaDisplayMode = '';

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $akTextPlaceholder = '';

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->akTextareaDisplayMode;
    }

    /**
     * @param mixed $mode
     */
    public function setMode($mode)
    {
        $this->akTextareaDisplayMode = $mode;
    }

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
