<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Controller\Element\Dashboard\Express\Entries\Header;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Event\Event;
use Concrete\Core\Express\Form\Context\DashboardFormContext;
use Concrete\Core\Express\Form\Control\SaveHandler\SaveHandlerInterface;
use Concrete\Core\Express\Form\OwnedEntityForm;
use Concrete\Core\Express\Form\Renderer;
use Concrete\Core\Form\Context\ContextFactory;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\Category;
use Concrete\Core\Tree\Type\ExpressEntryResults;

abstract class DashboardExpressEntityPageController extends DashboardExpressEntriesPageController
{

    protected function getEntity(\Concrete\Core\Tree\Node\Type\ExpressEntryResults $parent = null)
    {
        if (!method_exists($this, 'getEntityName')) {
            throw new \RuntimeException(t('Unless you override getEntity() you must define a method named getEntityName'));
        } else {
            return $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entity')
                ->findOneByName($this->getEntityName());
        }
    }

    protected function getResultsTreeNodeObject()
    {
        return Node::getByID($this->getEntity()->getEntityResultsNodeId());
    }

    public function view($folder = null)
    {
        $permissions = new \Permissions($this->getEntity());
        if ($permissions->canAddExpressEntries()) {
            $header = new Header($this->getEntity(), $this->getPageObject());
            $this->set('headerMenu', $header);
        }
        $this->renderList($folder);
    }

    public function create_entry($id = null, $owner_entry_id = null)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $entity = $r->findOneById($id);
        if (!is_object($entity)) {
            $this->redirect('/dashboard/express/entries');
        }
        if ($owner_entry_id) {
            $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entry');
            $entry = $r->findOneById($owner_entry_id);
        }
        $permissions = new \Permissions($entity);
        if (!$permissions->canAddExpressEntries()) {
            throw new \Exception(t('You do not have access to add entries of this entity type.'));
        }
        $this->set('entity', $entity);
        $form = $entity->getDefaultEditForm();
        if (is_object($entry) && $entry->getEntity() == $entity->getOwnedBy()) {
            $form = new OwnedEntityForm($form, $entry);
            $this->set('backURL', $this->getViewEntryURL($entry));
        } else {
            $this->set('backURL', $this->getBackURL($entity));
        }

        $express = \Core::make('express');
        $controller = $express->getEntityController($entity);
        $factory = new ContextFactory($controller);
        $context = $factory->getContext(new DashboardFormContext());

        $renderer = new Renderer(
            $context,
            $form
        );
        $this->set('renderer', $renderer);
        $this->render('/dashboard/express/entries/create', false);
    }

}
