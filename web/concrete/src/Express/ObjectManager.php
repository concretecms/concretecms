<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\AttributeKey\AttributeKey;
use Concrete\Core\Entity\AttributeValue\AttributeValue;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Express\Form\Control\SaveHandler\SaveHandlerInterface;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Express\Entity;
use Config;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EntityManagerFactory.
 *
 * @package Concrete\Core\Express
 * The backend entity manager hooks into Doctrine and is called by the front-end
 * entity manager.
 */
class ObjectManager
{
    protected $application;
    protected $entityManager;
    protected $namespace;
    protected $category;

    public function __construct(ExpressCategory $category, EntityManager $entityManager, Application $application)
    {
        $this->category = $category;
        $this->entityManager = $entityManager;
        $this->application = $application;
        $this->namespace = $application['config']->get('express.entity_classes.namespace');
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param mixed $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    public function getClassName(Entity $entity)
    {
        return '\\' . $this->getNamespace() . '\\' . $entity->getName();
    }

    public function create(Entity $entity)
    {
        $class = $this->getClassName($entity);
        $entity = new $class();

        return $entity;
    }

    public function saveFromRequest(Form $form, BaseEntity $entity, Request $request)
    {
        foreach ($form->getControls() as $control) {
            $type = $control->getControlType();
            /*
             * @var $type \Concrete\Core\Express\Form\Control\Type\TypeInterface
             */
            $saver = $type->getSaveHandler($control);
            if ($saver instanceof SaveHandlerInterface) {
                $saver->saveFromRequest($this, $control, $entity, $request);
            }
        }
        $this->save($entity);
    }

    public function setAttribute(BaseEntity $entity, $handleOrKey, $value)
    {
        if (is_object($handleOrKey)) {
            $key = $handleOrKey;
        } else {
            $key = $this->category->getAttributeKeyByHandle($handleOrKey);
        }
        $method = camelcase($key->getAttributeKeyHandle());
        $method = "set{$method}";

        $value = $key->getController()->saveValue($value);
        $entity->$method($value);
    }

    public function save(BaseEntity $entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    protected function getEntityOrName($entityOrName)
    {
        if ($entityOrName instanceof Entity) {
            $entity = $entityOrName;
        } else {
            $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
            $entity = $r->findOneByName($entityOrName);
        }

        return $entity;
    }

    public function getList($entityOrName)
    {
        $entity = $this->getEntityOrName($entityOrName);

        return new ObjectList($this, $entity);
    }

    public function getByID($entityOrName, $id)
    {
        $entity = $this->getEntityOrName($entityOrName);
        $r = $this->entityManager->getRepository($this->getClassName($entity));

        return $r->findOneById($id);
    }
}
