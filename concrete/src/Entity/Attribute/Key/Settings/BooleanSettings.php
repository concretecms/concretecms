<?php
namespace Concrete\Core\Entity\Attribute\Key\Settings;

use Concrete\Core\Entity\Attribute\Value\Value\BooleanValue;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atBooleanSettings")
 */
class BooleanSettings extends Settings
{
    /**
     * @ORM\Column(type="boolean")
     */
    protected $akCheckedByDefault = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $checkboxLabel;

    /**
     * @return mixed
     */
    public function getCheckboxLabel()
    {
        return $this->checkboxLabel;
    }

    /**
     * @param mixed $checkboxLabel
     */
    public function setCheckboxLabel($checkboxLabel)
    {
        $this->checkboxLabel = $checkboxLabel;
    }

    public function getAttributeTypeHandle()
    {
        return 'boolean';
    }

    /**
     * @return mixed
     */
    public function isCheckedByDefault()
    {
        return $this->akCheckedByDefault;
    }

    /**
     * @param mixed $isCheckedByDefault
     */
    public function setIsCheckedByDefault($isCheckedByDefault)
    {
        $this->akCheckedByDefault = $isCheckedByDefault;
    }

}
