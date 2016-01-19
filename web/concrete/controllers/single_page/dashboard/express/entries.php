<?php
namespace Concrete\Controller\SinglePage\Dashboard\Express;

use Concrete\Core\Entity\Express\Entity;
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
        $entity = $r->findOneById($id);
        if (!is_object($entity)) {
            $this->redirect('/dashboard/express');
        }
        $this->set('entity', $entity);
        $manager = \Core::make('express');
        $list = $manager->getList($entity->getName());

        if ($this->request->query->has($list->getQuerySortDirectionParameter())) {
            $direction = $this->request->query->get($list->getQuerySortDirectionParameter());
        }
        if ($this->request->query->has($list->getQuerySortColumnParameter())) {
            $value = $this->request->query->get($list->getQuerySortColumnParameter());
            $column = $entity->getResultColumnSet();
            $value = $column->getColumnByKey($value);
            if (is_object($value)) {
                $list->sanitizedSortBy($value->getColumnKey(), $direction);
            }
        }

        $result = new Result($entity->getResultColumnSet(), $list, \URL::to('/dashboard/express/entries/', $id));
        $this->set('list', $list);
        $this->set('result', $result);
        $this->set('results', $list->getResults());
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
