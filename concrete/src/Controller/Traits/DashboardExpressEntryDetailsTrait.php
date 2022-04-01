<?php

namespace Concrete\Core\Controller\Traits;

use Concrete\Core\Navigation\Item\Item;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\Context\DashboardFormContext;
use Concrete\Core\Express\Form\Context\DashboardViewContext;
use Concrete\Core\Express\Form\OwnedEntityForm;
use Concrete\Core\Express\Form\Processor\ProcessorInterface;
use Concrete\Core\Express\Form\Renderer;
use Concrete\Core\Form\Context\ContextFactory;
use Concrete\Core\Permission\Checker;

/**
 * Adds viewing, creation, update and deletion to a page.
 */
trait DashboardExpressEntryDetailsTrait
{

    protected function getBackURL(Entity $entity)
    {
        return \URL::to($this->getPageObject()
                            ->getCollectionPath(), 'results', $entity->getId());
    }

    protected function getCreateURL(Entity $entity, Entry $ownedBy = null)
    {
        $ownedByID = null;
        if (is_object($ownedBy)) {
            $ownedByID = $ownedBy->getID();
        }

        return \URL::to($this->getPageObject()
                            ->getCollectionPath(), 'create_entry', $entity->getID(), $ownedByID);
    }

    protected function getEditEntryURL(Entry $entry)
    {
        return \URL::to($this->getPageObject()
                            ->getCollectionPath(), 'edit_entry', $entry->getID());
    }

    protected function getViewEntryURL(Entry $entry)
    {
        return \URL::to($this->getPageObject()
                            ->getCollectionPath(), 'view_entry', $entry->getID());
    }

    public function create_entry($id = null, $owner_entry_id = null)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $entity = $r->findOneById($id);
        if (!is_object($entity)) {
            return $this->buildRedirect('/dashboard/express/entries');
        }

        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entry');
        $entry = $r->findOneById($owner_entry_id);
        $permissions = new Checker($entity);
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
        $this->set('pageTitle', t('Add %s', $entity->getEntityDisplayName()));
        $this->render('/dashboard/express/entries/create', false);
    }

    public function delete_entry()
    {
        $entry = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry')
            ->findOneById($this->request->request->get('entry_id'));

        $permissions = new \Permissions($entry);
        if (!$permissions->canDeleteExpressEntry()) {
            $this->error->add(t('You do not have access to delete entries of this entity type.'));
        }
        if (!$this->token->validate('delete_entry')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $entity = $entry->getEntity();
            $url = $this->getBackURL($entity);
            $controller = \Core::make('express')->getEntityController($entity);
            $manager = $controller->getEntryManager($this->request);
            $manager->deleteEntry($entry);

            $this->flash('success', t('Entry deleted successfully.'));
            $this->redirect($url);
        }
    }

    public function view_entry($id = null)
    {
        $entry = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry')
            ->findOneById($id);

        $permissions = new \Permissions($entry);
        if (!$permissions->canViewExpressEntry()) {
            throw new \Exception(t('Access Denied'));
        }

        $this->set('entry', $entry);
        $this->set('entity', $entry->getEntity());
        $entity = $entry->getEntity();
        $this->entityManager->refresh($entity); // sometimes this isn't eagerly loaded (?)

        $express = \Core::make('express');
        $controller = $express->getEntityController($entity);
        $factory = new ContextFactory($controller);
        $context = $factory->getContext(new DashboardViewContext());

        $renderer = new Renderer(
            $context,
            $entity->getDefaultViewForm()
        );

        $this->set('renderer', $renderer);
        if ($entity->getOwnedBy()) {
            // the back url is the detail of what is the owner
            $ownerEntry = $entry->getOwnedByEntry();
            if (is_object($ownerEntry)) {
                $this->set('backURL', $this->getViewEntryURL($ownerEntry));
            }
        } else {
            $this->set('backURL', $this->getBackURL($entry->getEntity()));
        }
        if ($permissions->canEditExpressEntry()) {
            $this->set('editURL', $this->getEditEntryURL($entry));
        }
        if ($permissions->canDeleteExpressEntry()) {
            $this->set('allowDelete', true);
        } else {
            $this->set('allowDelete', false);
        }
        $subEntities = [];
        foreach ($entry->getEntity()->getAssociations() as $association) {
            if ($association->isOwningAssociation()) {
                $subEntities[] = $association->getTargetEntity();
            }
        }

        $factory = $this->createBreadcrumbFactory();
        $breadcrumb = $factory->getBreadcrumb($this->getPageObject(), $entry);
        $this->setBreadcrumb($breadcrumb);

        $this->set('subEntities', $subEntities);
        $this->set('pageTitle', t('View %s Entry', $entity->getEntityDisplayName()));
        $this->render('/dashboard/express/entries/view_entry', false);
    }

    public function edit_entry($id = null)
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

        $renderer = new Renderer(
            $context,
            $entity->getDefaultEditForm()
        );

        $factory = $this->createBreadcrumbFactory();
        $breadcrumb = $factory->getBreadcrumb($this->getPageObject(), $entry);
        $breadcrumb->add(new Item('#', t('Edit Entry')));
        $this->setBreadcrumb($breadcrumb);

        $this->set('renderer', $renderer);
        $this->set('backURL', $this->getBackURL($entry->getEntity()));
        $this->set('pageTitle', t('Edit %s Entry', $entity->getEntityDisplayName()));
        $this->render('/dashboard/express/entries/update', false);
    }

    public function submit($id = null)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $entity = $r->findOneById($id);

        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Form');
        $form = $r->findOneById($this->request->request->get('express_form_id'));

        $entry = null;
        if ($this->request->request->has('entry_id')) {
            $entry = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry')
                ->findOneById($this->request->request->get('entry_id'));
        }

        if (null !== $form) {
            $express = $this->app->make('express');
            $controller = $express->getEntityController($entity);
            $processor = $controller->getFormProcessor();
            $validator = $processor->getValidator($this->request);

            if (null === $entry) {
                $validator->validate($form);
            } else {
                $validator->validate($form, $entry);
            }

            $this->error = $validator->getErrorList();
            if ($this->error->has()) {
                if (null === $entry) {
                    $this->create_entry($entity->getID());
                } else {
                    $this->edit_entry($entry->getID());
                }
            } else {
                $notifier = $controller->getNotifier();
                $notifications = $notifier->getNotificationList();

                $manager = $controller->getEntryManager($this->request);
                if (null === $entry) {
                    // create
                    $entry = $manager->addEntry($entity, $this->getSite());
                    $entry = $manager->saveEntryAttributesForm($form, $entry);
                    $notifier->sendNotifications($notifications, $entry, ProcessorInterface::REQUEST_TYPE_ADD);

                    $this->flash(
                        'success',
                        tc(/*i18n: %s is an Express entity name*/'Express', 'New record %s added successfully.', $entity->getEntityDisplayName())
                        . '<br /><br />'
                        . '<a class="btn btn-secondary" href="' . \URL::to(\Page::getCurrentPage(), 'view_entry', $entry->getID()) . '">' . t('View Record Here') . '</a>',
                        true
                    );
                    if (is_object($entry->getOwnedByEntry())) {
                        $this->redirect(\URL::to(\Page::getCurrentPage(), 'create_entry', $entity->getID(), $entry->getOwnedByEntry()->getID()));
                    } else {
                        $this->redirect(\URL::to(\Page::getCurrentPage(), 'create_entry', $entity->getID()));
                    }
                } else {
                    // update
                    $manager->saveEntryAttributesForm($form, $entry);
                    $notifier->sendNotifications($notifications, $entry, ProcessorInterface::REQUEST_TYPE_UPDATE);
                    $this->flash('success', t('%s updated successfully.', $entity->getEntityDisplayName()));
                    return $this->buildRedirect($this->getBackURL($entity));
                }
            }
        } else {
            throw new \Exception(t('Invalid form.'));
        }
    }


}
