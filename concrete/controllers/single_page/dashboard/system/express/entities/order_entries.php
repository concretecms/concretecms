<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Express\Entities;

use Concrete\Core\Controller\Traits\DashboardExpressEntityBreadcrumbTrait;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Page\Controller\DashboardPageController;

class OrderEntries extends DashboardPageController
{
    use DashboardExpressEntityBreadcrumbTrait;

    public function view($id = null)
    {
        $entity = $id ? $this->entityManager->find('Concrete\Core\Entity\Express\Entity', $id) : null;
        if (!$entity) {
            $this->flash('error', t('Invalid express entity.'));

            return $this->buildRedirect(['/dashboard/system/express/entities']);
        }
        if (!$entity->supportsCustomDisplayOrder()) {
            $this->flash('error', t('This data object does not support a custom display order of its entries.'));

            return $this->buildRedirect(['/dashboard/system/express/entities', 'view_entity', $entity->getId()]);
        }
        $provider = $this->app->make('Concrete\Core\Express\Search\SearchProvider', array('entity' => $entity, 'category' => $entity->getAttributeKeyCategory()));
        $this->set('entity', $entity);
        $list = new EntryList($entity);
        $list->sortByDisplayOrderAscending();
        $this->set('result', $provider->createSearchResultObject($provider->getCurrentColumnSet(), $list));

        return null;
    }

    public function save($id = null)
    {
        $response = $this->view($id);
        if ($response !== null) {
            return $response;
        }
        if (!$this->token->validate('save')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $entity = $this->get('entity');

            $displayOrder = 0;
            foreach($this->request->request->get('entry') as $entryID) {
                $entry = $this->entityManager->find('Concrete\Core\Entity\Express\Entry', $entryID);
                if ($entry) {
                    $entry->setEntryDisplayOrder($displayOrder);
                    $this->entityManager->persist($entry);
                    $displayOrder++;
                }
            }
            $this->entityManager->flush();
            $this->flash('success', t('Display order updated successfully.'));
            $this->redirect('/dashboard/system/express/entities/order_entries', $entity->getId());
        }
    }

}
