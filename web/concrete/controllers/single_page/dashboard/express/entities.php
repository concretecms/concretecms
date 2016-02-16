<?php
namespace Concrete\Controller\SinglePage\Dashboard\Express;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Page\Controller\DashboardPageController;

class Entities extends DashboardPageController
{
    public function add()
    {
        $this->set('pageTitle', t('Add Data Object'));
        if ($this->request->isMethod('POST')) {
            if (!$this->token->validate('add_entity')) {
                $this->error->add($this->token->getErrorMessage());
            }
            if (!$this->request->request->get('name')) {
                $this->error->add(t('You must give your data object a name.'));
            }
            if (!$this->request->request->get('handle')) {
                $this->error->add(t('You must create a handle for your data object. The handle must be all lowercase, and contain no spaces.'));
            }
            if (!$this->error->has()) {
                $entity = new Entity();
                $entity->setName($this->request->request->get('name'));
                $entity->setHandle($this->request->request->get('handle'));
                $entity->setDescription($this->request->request->get('description'));
                $this->entityManager->persist($entity);
                $this->entityManager->flush();

                $indexer = $entity->getAttributeKeyCategory()->getSearchIndexer();
                if (is_object($indexer)) {
                    $indexer->createRepository($entity->getAttributeKeyCategory());
                }

                $this->flash('success', t('Object added successfully.'));
                $this->redirect('/dashboard/express/entities', 'view_entity', $entity->getId());
            }
        }

        $this->render('/dashboard/express/entities/add');
    }

    /*
    public function delete_batch()
    {
        if (!$this->token->validate('delete_batch')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $r = $this->entityManager->getRepository('\PortlandLabs\Concrete5\MigrationTool\Entity\Export\Batch');
            $batch = $r->findOneById($this->request->request->get('id'));
            if (is_object($batch)) {
                foreach ($batch->getObjectCollections() as $collection) {
                    $this->entityManager->remove($collection);
                }
                $batch->setObjectCollections(new ArrayCollection());
                $this->entityManager->flush();
                $this->entityManager->remove($batch);
                $this->entityManager->flush();
                $this->flash('success', t('Batch removed successfully.'));
                $this->redirect('/dashboard/system/migration/export');
            }
        }
        $this->view();
    }

    */
    public function view()
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $entities = $r->findAll(array(), array('name' => 'asc'));
        $this->set('entities', $entities);
    }

    public function view_entity($id = null)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $entity = $r->findOneById($id);
        if (is_object($entity)) {
            $this->set('entity', $entity);
            $this->set('pageTitle', t('Object Details'));
            $this->render('/dashboard/express/entities/view_details');
        } else {
            $this->view();
        }
    }
}
