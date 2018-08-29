<?php
namespace Concrete\Controller\SinglePage\Dashboard\Express;

use Concrete\Controller\Element\Dashboard\Express\Entries\Header;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Page\Controller\DashboardExpressEntityPageController;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Tree\Node\Node;

class Entries extends DashboardExpressEntityPageController
{

    /**
     * @var $entity Entity
     */
    protected $entity;

    public function getEntity(\Concrete\Core\Tree\Node\Type\ExpressEntryResults $parent = null)
    {
        if ($this->entity) {
            return $this->entity;
        } else {
            return $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entity')
                ->findOneByResultsNode($parent);
        }
    }

    protected function getBackURL(Entity $entity)
    {
        return \URL::to($this->getPageObject()
            ->getCollectionPath(), 'view', $entity->getID(),
            $entity->getEntityResultsNodeID());
    }


    public function view($entity = null, $folder = null)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        if ($entity) {
            $entity = $r->findOneById($entity);
        }
        if (isset($entity) && is_object($entity)) {
            $this->entity = $entity;
            $this->set('entity', $entity);
            $this->renderList($folder);
            $permissions = new \Permissions($this->getEntity());
            if ($permissions->canAddExpressEntries()) {
                $header = new Header($entity, $this->getPageObject());
                $this->set('headerMenu', $header);
                $this->set('pageTitle', t('View %s Entries', $this->getEntity()->getName()));
            }
        } else {
            $this->set('pageTitle', t('View Express Entities'));
            $this->set('entities', $r->findPublicEntities());
        }
    }

      public function edit_entry($id = null, $formId = null)
  {
    $entry = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry')
      ->findOneById($id);

    $permissions = new \Permissions($entry);
    if (!$permissions->canEditExpressEntry()) {
      throw new \Exception(t('Access Denied'));
    }

    $entity = $entry->getEntity();
    $this->set('entry', $entry);
    $this->set('entity', $entity);
    $entity = $entry->getEntity();
    $this->entityManager->refresh($entity); // sometimes this isn't eagerly loaded (?)

    $express = \Core::make('express');
    $controller = $express->getEntityController($entity);
    $factory = new ContextFactory($controller);
    $context = $factory->getContext(new DashboardFormContext());
    $customForm = false;
    if($formId != null){
      $customForm = $this->getForm($entity, $formId);
    }
    $renderer = new Renderer(
      $context,
      $customForm ? $customForm : $entity->getDefaultEditForm()
    );

    $this->set('renderer', $renderer);
    $this->set('backURL', $this->getBackURL($entry->getEntity()));
    $this->set('editURL', $this->getEditEntryURL($entry));
    $this->set('currentForm', $customForm ? $customForm : $entity->getDefaultEditForm());
    $this->render('/dashboard/express/entries/update', false);
  }

  public function create_entry($id = null, $owner_entry_id = null, $formId = null)
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
    $customForm = false;
    if($formId != null){
      $customForm = $this->getForm($entity, $formId);
    }
    $renderer = new Renderer(
      $context,
      $customForm ? $customForm : $form
    );
    $this->set('renderer', $renderer);
    $this->set('url', $this->getCreateURL($entity, $entry) . ($owner_entry_id == null ? '/' : ''));
    $this->set('currentForm', $customForm ? $customForm : $form);
    $this->render('/dashboard/express/entries/create', false);
  }

  private function getForm(Entity $entity, $formId)
  {
    $form = null;
    if ($formId) {
      try {
        $form = $entity->getForms()->filter(function ($form) use ($formId) {
          return $form->getId() == $formId;
        })->first();
      } catch (\Exception $ex) {
      }
    }
    return $form;
  }

}
