<?php
namespace Concrete\Core\Entity\Attribute\Key\Settings;

use Concrete\Core\Entity\Attribute\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperClass
 */
abstract class Settings
{

    protected $type;

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\Key")
     * @ORM\JoinColumn(name="akID", referencedColumnName="akID")
     */
    protected $key;

    /**
     * @return mixed
     */
    public function getAttributeKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setAttributeKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getAttributeType()
    {
        if (!isset($this->type)) {
            $this->type = $this->key->getAttributeType();
        }
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setAttributeType(Type $type)
    {
        $this->type = $type;
    }


    public function mergeAndPersist(EntityManagerInterface $entityManager)
    {
        $settings = $entityManager->merge($this);
        $entityManager->persist($settings);
        return $settings;
    }

    public function createController()
    {
        $type = $this->getAttributeType();
        return $type->getController();
    }

    public function getController()
    {
        $controller = $this->createController();

        return $controller;
    }
}
