<?php

namespace Concrete\Core\Entity\File\Image;

use Concrete\Core\Entity\PackageTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="Editor"
 * )
 */
class Editor
{
    use PackageTrait;

    /**
     * @ORM\Column(type="string", options={"unsigned": true}, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", options={"unsigned": true})
     */
    protected $handle;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Editor
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param mixed $handle
     * @return Editor
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
        return $this;
    }

}