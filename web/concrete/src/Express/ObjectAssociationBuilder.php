<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Express\ManyToManyAssociation;
use Concrete\Core\Entity\Express\ManyToOneAssociation;
use Concrete\Core\Entity\Express\OneToManyAssociation;
use Concrete\Core\Entity\Express\OneToOneAssociation;
use Doctrine\ORM\EntityManagerInterface;

class ObjectAssociationBuilder
{

    protected $application;
    protected $entityManager;
    protected $subjectEntity;

    public function __construct(EntityManagerInterface $entityManager, Application $application)
    {
        $this->application = $application;
        $this->entityManager = $entityManager;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }


    protected function getSubjectEntity($entityName)
    {
        if (!isset($this->subjectEntity)) {
            $repository = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
            $this->subjectEntity = $repository->findOneByName($entityName);
        }
        return $this->subjectEntity;
    }

    protected function addRelation($relation, $subjectEntity, $targetEntity, $property = null)
    {
        $subjectEntity = $this->getSubjectEntity($subjectEntity);
        var_dump_safe($subjectEntity);
        exit;
    }

    public function addManyToOne($subjectEntity, $targetEntity, $property = null)
    {
        $this->addRelation(new ManyToOneAssociation(),
            $subjectEntity, $targetEntity, $property);
    }

    public function addOneToMany($subjectEntity, $targetEntity, $property = null)
    {
        $this->addRelation(new OneToManyAssociation(),
            $subjectEntity, $targetEntity, $property);
    }

    public function addManyToMany($subjectEntity, $targetEntity, $property = null)
    {
        $this->addRelation(new ManyToManyAssociation(),
            $subjectEntity, $targetEntity, $property);
    }

    public function addOneToOne($subjectEntity, $targetEntity, $property = null)
    {
        $this->addRelation(new OneToOneAssociation(),
            $subjectEntity, $targetEntity, $property);
    }







}