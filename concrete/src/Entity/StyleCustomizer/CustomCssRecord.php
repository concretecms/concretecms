<?php
namespace Concrete\Core\Entity\StyleCustomizer;

use Database;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="StyleCustomizerCustomCssRecords")
 */
class CustomCssRecord
{
    /**
     * @ORM\Column(type="text")
     */
    protected $value;

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $sccRecordID;

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getRecordID()
    {
        return $this->sccRecordID;
    }

    public function save()
    {
        $em = \ORM::entityManager();
        $em->persist($this);
        $em->flush();
    }
}
