<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Express;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Type\ExpressEntryResults;

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

                // Create a results node
                $tree = ExpressEntryResults::get();
                $node = $tree->getRootTreeNodeObject();
                $node = \Concrete\Core\Tree\Node\Type\ExpressEntryResults::add($entity->getName(), $node);

                $entity->setEntityResultsNodeId($node->getTreeNodeID());
                $this->entityManager->persist($entity);
                $this->entityManager->flush();

                $indexer = $entity->getAttributeKeyCategory()->getSearchIndexer();
                if (is_object($indexer)) {
                    $indexer->createRepository($entity->getAttributeKeyCategory());
                }

                $this->flash('success', t('Object added successfully.'));
                $this->redirect('/dashboard/system/express/entities', 'view_entity', $entity->getId());
            }
        }

        $this->render('/dashboard/system/express/entities/add');
    }

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
            $this->render('/dashboard/system/express/entities/view_details');
        } else {
            $this->view();
        }
    }

    public function edit($id = null)
    {
        $tree = ExpressEntryResults::get();
        $this->set('tree', $tree);
        $this->requireAsset('core/topics');
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $this->entity = $r->findOneById($id);
        if (is_object($this->entity)) {
            $node = Node::getByID($this->entity->getEntityResultsNodeId());
            if (is_object($node)) {
                $folder = $node->getTreeNodeParentObject();
                $this->set('folder', $folder);
            }
            $this->set('entity', $this->entity);
            $this->set('pageTitle', t('Edit Entity'));
            $this->render('/dashboard/system/express/entities/edit');
        } else {
            $this->view();
        }
    }


    public function update($id = null)
    {
        $this->edit($id);
        $entity = $this->entity;
        if (!$this->token->validate('update_entity')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->request->request->get('name')) {
            $this->error->add(t('You must give your data object a name.'));
        }
        if (!$this->request->request->get('handle')) {
            $this->error->add(t('You must create a handle for your data object. The handle must be all lowercase, and contain no spaces.'));
        }
        if (!$this->request->request->get('entity_results_node_id')) {
            $this->error->add(t('You must choose where the results for your entity are going live.'));
        }
        if (!$this->error->has()) {
            $entity->setName($this->request->request->get('name'));
            $entity->setHandle($this->request->request->get('handle'));
            $entity->setDescription($this->request->request->get('description'));

            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            $resultsNode = Node::getByID($entity->getEntityResultsNodeId());
            $folder = Node::getByID($this->request->request('entity_results_node_id'));
            if (is_object($folder)) {
                $resultsNode->move($folder);
            }

            $this->flash('success', t('Object updated successfully.'));
            $this->redirect('/dashboard/system/express/entities', 'view_entity', $entity->getId());
        }
    }

}
