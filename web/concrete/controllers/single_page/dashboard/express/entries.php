<?php
namespace Concrete\Controller\SinglePage\Dashboard\Express;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Search\Result\Result;

class Entries extends DashboardPageController
{
    public function view($id = null)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        /**
         * @var $entity Entity
         */
        if ($id) {
            $entity = $r->findOneById($id);
        }
        if (isset($entity) && is_object($entity)) {
            $this->set('entity', $entity);

            $search = new \Concrete\Controller\Search\Express\Entries();
            $search->search($entity);

            $this->set('list', $search->getListObject());
            $this->set('searchController', $search);
        } else {
            $this->set('entities', $r->findByIncludeInPublicList(true));
        }
    }

    public function delete_entry($id = null)
    {
        $this->view($id);
        if (!$this->token->validate('delete_entry')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $manager = \Core::make('express');
            $entry = $manager->getById($this->get('entity'), $this->request->request->get('entry_id'));
            $this->entityManager->remove($entry);
            $this->entityManager->flush();
            $this->flash('success', t('Entry deleted successfully.'));
            $this->redirect('/dashboard/express/entries', $id);
        }
    }

    public function view_entry($entityId = null, $id = null)
    {
        $this->view($entityId);
        $manager = \Core::make('express');
        $o = $manager->getByID($this->get('entity'), $id);
        $renderer = \Core::make('Concrete\Core\Express\Form\ViewRenderer');

        $this->set('entry', $o);
        $this->set('expressForm', $this->get('entity')->getForms()[0]);
        $this->set('renderer', $renderer);
        $this->render('/dashboard/express/entries/view_entry');
    }
}
