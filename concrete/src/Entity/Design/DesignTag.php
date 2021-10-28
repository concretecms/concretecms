<?php
namespace Concrete\Core\Entity\Design;

use Concrete\Core\Design\Tag\TagInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="DesignTags")
 */
class DesignTag implements TagInterface
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $value;

    /**
     * @return mixed
     */
    public function getDesignTagID()
    {
        return $this->designTagID;
    }

    /**
     * @return mixed
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }



}
