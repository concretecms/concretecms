<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Express\Entities;

use Concrete\Core\Express\EntryList;
use Concrete\Core\Page\Controller\DashboardPageController;

class OrderEntries extends DashboardPageController
{

    public function view($id = null)
    {
        if ($id) {
            $entity = $this->entityManager->find('Concrete\Core\Entity\Express\Entity', $id);
        }
        if (isset($entity) && is_object($entity) && $entity->supportsCustomDisplayOrder()) {
            $provider = $this->app->make('Concrete\Core\Express\Search\SearchProvider', array($entity, $entity->getAttributeKeyCategory()));
            $this->set('entity', $entity);
            $list = new EntryList($entity);
            $list->sortByDisplayOrderAscending();
            $this->set('result', $provider->createSearchResultObject($provider->getCurrentColumnSet(), $list));
        } else {
            $this->redirect('/dashboard/system/express/entities');
        }
    }

    public function save($id = null)
    {
        $this->view($id);
        if (!$this->token->validate('save')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $entity = $this->get('entity');

            $displayOrder = 0;
            foreach($this->request->request->get('entry') as $entryID) {
                $entry = $this->entityManager->find('Concrete\Core\Entity\Express\Entry', $entryID);
                $entry->setEntryDisplayOrder($displayOrder);
                $this->entityManager->persist($entry);
                $displayOrder++;
            }
            $this->entityManager->flush();
            $this->flash('success', t('Display order updated successfully.'));
            $this->redirect('/dashboard/system/express/entities/order_entries', $entity->getId());
        }
    }

}
