<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Express\Entities;

use Concrete\Core\Page\Controller\DashboardPageController;

class Associations extends DashboardPageController
{
    protected $repository;
    protected $associationRepository;
    protected $entity;

    public function on_start()
    {
        parent::on_start();
        $this->repository = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $this->associationRepository = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Association');
    }

    public function add($id = null)
    {
        $entity = $this->repository->findOneById($id);
        if (is_object($entity)) {
            $this->set('entity', $entity);
            $entities = $this->repository->findAll();
            $this->set('entities', $entities);
            $types = array('' => t('** Select Type'));
            $validTypes = [
                'ManyToOne' => t('Many to One'),
                'OneToMany' => t('One to Many'),
                'OneToOne' => t('One to One'),
                'ManyToMany' => t('Many to Many'),
            ];
            $types += $validTypes;
            $this->set('types', $types);
            $this->set('pageTitle', t('Add Association'));

            if ($this->request->isMethod('POST')) {

                $entityIDs = array();
                foreach($entities as $targetEntity) {
                    $entityIDs[] = $targetEntity->getID();
                }
                if (!$this->token->validate('add_association')) {
                    $this->error->add($this->token->getErrorMessage());
                }
                if (!in_array($this->request->request->get('target_entity'), $entityIDs)) {
                    $this->error->add(t('Invalid target entity.'));
                }
                if (!in_array($this->request->request->get('type'), array_keys($validTypes))) {
                    $this->error->add(t('You must choose what type of association this is.'));
                }

                if (!$this->request->request->get('target_property_name')) {
                    $this->error->add(t('You must give your association a target property name.'));
                }
                if (!$this->error->has()) {
                    $builder = \Core::make('express/builder/association');
                    $targetEntity = $this->repository->findOneById($this->request->request->get('target_entity'));
                    /*
                     * @var $builder \Concrete\Core\Express\ObjectAssociationBuilder
                     */
                    switch ($this->request->request->get('type')) {
                        case 'ManyToOne':
                            $builder->addManyToOne(
                                $entity, $targetEntity, $this->request->request->get('target_property_name'), $this->request->request->get('inversed_property_name')
                            );
                            break;
                        case 'OneToOne':
                            $builder->addOneToOne(
                                $entity, $targetEntity, $this->request->request->get('target_property_name'), $this->request->request->get('inversed_property_name')
                            );
                            break;
                        case 'ManyToMany':
                            $builder->addManyToMany(
                                $entity, $targetEntity, $this->request->request->get('target_property_name'), $this->request->request->get('inversed_property_name')
                            );
                            break;
                        case 'OneToMany':
                            $builder->addOneToMany(
                                $entity, $targetEntity, $this->request->request->get('target_property_name'), $this->request->request->get('inversed_property_name')
                            );
                            break;
                    }
                    $this->entityManager->persist($entity);
                    $this->entityManager->persist($targetEntity);
                    $this->entityManager->flush();

                    $this->flash('success', t('Association added successfully.'));
                    $this->redirect('/dashboard/system/express/entities/associations', $entity->getId());
                }
            }

            $this->render('/dashboard/system/express/entities/associations/add');
        } else {
            $this->redirect('/dashboard/system/express/entities');
        }
    }
    public function view($id = null)
    {
        $entity = $this->repository->findOneById($id);
        if (is_object($entity)) {
            $this->entity = $entity;
            $this->set('entity', $entity);
            $this->set('associations', $entity->getAssociations());
            $this->set('pageTitle', t('Associations'));
        } else {
            $this->redirect('/dashboard/system/express/entities');
        }
    }

    public function edit($id = null)
    {
        $this->view_association_details($id);
        $this->render('/dashboard/system/express/entities/associations/edit');
    }

    public function delete_association($id = null)
    {
        $this->view($id);
        $association = $this->associationRepository->findOneById($this->request->request->get('association_id'));
        if (!is_object($association)) {
            $this->error->add(t('Invalid association object.'));
        }
        if (!$this->token->validate('delete_association')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $this->entityManager->remove($association);
            $this->entityManager->flush();

            $this->flash('success', t('Association deleted successfully.'));
            $this->redirect('/dashboard/system/express/entities/associations', $id);
        } else {
            $this->view_association_details($this->request->request->get('association_id'));
        }
    }

    public function save_association($id = null)
    {
        $this->view($id);
        $association = $this->associationRepository->findOneById($this->request->request->get('association_id'));
        if (!is_object($association)) {
            $this->error->add(t('Invalid association object.'));
        }
        if (!$this->token->validate()) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {

            $association->setInversedByPropertyName($this->request->request->get('inversed_property_name'));
            $association->setTargetPropertyName($this->request->request->get('target_property_name'));
            if ($this->request->request->get('is_owned_by_association')) {
                $association->setIsOwnedByAssociation(true);
            } else {
                $association->setIsOwnedByAssociation(false);
            }
            if ($this->request->request->get('is_owning_association')) {
                $association->setIsOwningAssociation(true);
            } else {
                $association->setIsOwningAssociation(false);
            }

            $this->entityManager->persist($association);
            $this->entityManager->flush();

            $this->flash('success', t('Association saved successfully.'));
            $this->redirect('/dashboard/system/express/entities/associations', 'view_association_details', $association->getID());
        } else {
            $this->view_association_details($this->request->request->get('association_id'));
        }
    }


    public function view_association_details($id = null)
    {
        $association = $this->associationRepository->findOneById($id);
        if (is_object($association)) {
            $entity = $association->getSourceEntity();
            $this->set('entity', $entity);
            $this->set('association', $association);
            $this->set('formatter', $association->getFormatter());
            $this->set('pageTitle', t('Association Details'));
            $this->render('/dashboard/system/express/entities/associations/view_association');
        }
        if (is_object($entity)) {
        } else {
            $this->redirect('/dashboard/system/express/entities');
        }
    }
}
