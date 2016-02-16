<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Express\Entry;
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
    protected $category;

    public function __construct(ExpressCategory $category, EntityManager $entityManager, Application $application)
    {
        $this->category = $category;
        $this->entityManager = $entityManager;
        $this->application = $application;
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

    public function create(Entity $entity)
    {
        $entity = new Entry();
        $entity->setEntity($entity);

        return $entity;
    }

    public function saveFromRequest(Form $form, Entry $entry, Request $request)
    {
        foreach ($form->getControls() as $control) {
            $type = $control->getControlType();
            $saver = $type->getSaveHandler($control);
            if ($saver instanceof SaveHandlerInterface) {
                $saver->saveFromRequest($this, $control, $entry, $request);
            }
        }
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


    public function getByID($entityOrName, $id)
    {
        $entity = $this->getEntityOrName($entityOrName);
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry');


        return $r->findOneById($id);
    }

    /*

        public function create(Entity $entity)
        {
            $class = $this->getClassName($entity);
            $entity = new $class();

            return $entity;
        }


        public function setAttribute(Entity $entity, $handleOrKey, $value)
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

        public function save(Entity $entity)
        {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }

        public function getList($entityOrName)
        {
            $entity = $this->getEntityOrName($entityOrName);
            $list = new ObjectList($this, $entity);
            $set = $entity->getResultColumnSet();
            if (is_object($set)) {
                $sort = $set->getDefaultSortColumn();
                $list->sanitizedSortBy($sort->getColumnKey(), $sort->getColumnDefaultSortDirection());
            }
            return $list;
        }

        public function getByID($entityOrName, $id)
        {
            $entity = $this->getEntityOrName($entityOrName);
            $r = $this->entityManager->getRepository($this->getClassName($entity));

            return $r->findOneById($id);
        }*/
}
