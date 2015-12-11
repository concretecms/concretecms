<?
namespace Concrete\Controller\SinglePage\Dashboard\Express\Entities;

use \Concrete\Core\Page\Controller\DashboardPageController;

class Associations extends DashboardPageController
{

    protected $repository;
    protected $associationRepository;

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
            $entities = array();
            foreach($this->repository->findAll() as $targetEntity) {
                if ($targetEntity->getID() != $entity->getId()) {
                    $entities[$targetEntity->getID()] = $targetEntity->getName();
                }
            }
            $types = array('' => t('** Select Type'));
            $validTypes = [
                'ManyToOne' => t('Many to One'),
                'OneToMany' => t('One to Many'),
                'OneToOne' => t('One to One'),
                'ManyToMany' => t('Many to Many')
            ];
            $types += $validTypes;
            $this->set('entities', $entities);
            $this->set('types', $types);
            $this->set('pageTitle', t('Add Association'));

            if ($this->request->isMethod('POST')) {
                if (!$this->token->validate('add_association')) {
                    $this->error->add($this->token->getErrorMessage());
                }
                if (!in_array($this->request->request->get('target_entity'), array_keys($entities))) {
                    $this->error->add(t('Invalid target entity.'));
                }
                if (!in_array($this->request->request->get('type'), array_keys($validTypes))) {
                    $this->error->add(t('You must choose what type of association this is.'));
                }

                if (!$this->request->request->get('target_property_name')) {
                    $this->error->add(t('You must give your association a target property name.'));
                }
                if (!$this->error->has()) {
                    $builder = \Core::make('express.builder.association');
                    $targetEntity = $this->repository->findOneById($this->request->request->get('target_entity'));
                    /**
                     * @var $builder \Concrete\Core\Express\ObjectAssociationBuilder
                     */
                    switch($this->request->request->get('type')) {
                        case 'ManyToOne':
                            $builder->addManyToOne(
                                $entity, $targetEntity, $this->request->request->get('target_property_name')
                            );
                            break;
                        case 'OneToOne':
                            $builder->addOneToOne(
                                $entity, $targetEntity, $this->request->request->get('target_property_name')
                            );
                            break;
                        case 'ManyToMany':
                            $builder->addManyToMany(
                                $entity, $targetEntity, $this->request->request->get('target_property_name')
                            );
                            break;
                        case 'OneToMany':
                            $builder->addOneToMany(
                                $entity, $targetEntity, $this->request->request->get('target_property_name')
                            );
                            break;
                    }
                    $this->entityManager->persist($entity);
                    $this->entityManager->persist($targetEntity);
                    $this->entityManager->flush();

                    $this->flash('success', t('Associations added successfully.'));
                    $this->redirect('/dashboard/express/entities/associations', $entity->getId());
                }
            }


            $this->render('/dashboard/express/entities/associations/add');
        } else {
            $this->redirect('/dashboard/express/entities');
        }
    }
    public function view($id = null)
    {
        $entity = $this->repository->findOneById($id);
        if (is_object($entity)) {
            $this->set('entity', $entity);
            $this->set('associations', $entity->getAssociations());
            $this->set('pageTitle', t('Associations'));
        } else {
            $this->redirect('/dashboard/express/entities');
        }
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
            $this->redirect('/dashboard/express/entities/associations', $id);
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
            $this->render('/dashboard/express/entities/associations/view_association');

        }
        if (is_object($entity)) {
        } else {
            $this->redirect('/dashboard/express/entities');
        }
    }




}
