<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Entry\Manager;
use Concrete\Core\Express\Form\Validator;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Type\ExpressEntryResults;

abstract class DashboardExpressEntriesPageController extends DashboardPageController
{
    protected function getBackToListURL(Entity $entity)
    {
        return \URL::to($this->getPageObject()
            ->getCollectionPath(), 'view', $entity->getEntityResultsNodeID());
    }

    protected function getEditEntryURL(Entry $entry)
    {
        return \URL::to($this->getPageObject()
            ->getCollectionPath(), 'edit_entry', $entry->getID());
    }

    protected function getResultsTreeNodeObject()
    {
        $tree = ExpressEntryResults::get();

        return $tree->getRootTreeNodeObject();
    }

    protected function renderList($treeNodeParentID = null)
    {
        $nodes = null;
        $parent = null;
        if ($treeNodeParentID) {
            $parent = Node::getByID($treeNodeParentID);
            if (is_object($parent)) {
                $tree = $parent->getTreeObject();
                if (!($tree instanceof ExpressEntryResults)) {
                    unset($parent);
                }
            }
        }
        if (!isset($parent)) {
            $parent = $this->getResultsTreeNodeObject();
        }

        $this->set('breadcrumb', $this->getBreadcrumb($parent));

        if (isset($parent) && $parent instanceof \Concrete\Core\Tree\Node\Type\ExpressEntryResults) {
            // Get the express entry for which this applies.
            $entity = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entity')
                ->findOneByResultsNode($parent);
            $search = new \Concrete\Controller\Search\Express\Entries();
            $search->search($entity);
            $this->set('list', $search->getListObject());
            $this->set('searchController', $search);
            $this->set('entity', $entity);
            $this->render('/dashboard/express/entries/entries', false);
        } else {
            $parent->populateDirectChildrenOnly();
            $this->set('nodes', $parent->getChildNodes());
            $this->render('/dashboard/express/entries/folder', false);
        }
    }

    protected function getBreadcrumb(Node $node = null)
    {
        $c = $this->getPageObject();
        $breadcrumb = [[
            'active' => false,
            'name' => t('Results'),
            'url' => \URL::to($c),
        ]];

        if (is_object($node)) {
            $items = $node->getTreeNodeParentArray();
            $items = array_slice($items, 0, count($items) - 1);
            $items = array_reverse($items);
            $items[] = $node;
            for ($i = 1; $i < count($items); ++$i) {
                $item = $items[$i];
                $breadcrumb[] = [
                    'id' => $item->getTreeNodeID(),
                    'active' => $item->getTreeNodeID() == $node->getTreeNodeID(),
                    'name' => $item->getTreeNodeDisplayName(),
                    'url' => \URL::to('/dashboard/reports/forms', 'view', $item->getTreeNodeID()),
                ];
            }
        }

        if (count($breadcrumb) == 1) {
            array_pop($breadcrumb);
        }

        return $breadcrumb;
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
            $url = $this->getBackToListURL($entity);
            $this->entityManager->remove($entry);
            $this->entityManager->flush();
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

        $renderer = \Core::make('Concrete\Core\Express\Form\StandardViewRenderer');
        $this->set('entry', $entry);
        $this->set('entity', $entry->getEntity());
        $entity = $entry->getEntity();
        $this->entityManager->refresh($entity); // sometimes this isn't eagerly loaded (?)
        $this->set('expressForm', $entity->getDefaultViewForm());
        $this->set('renderer', $renderer);
        $this->set('backURL', $this->getBackToListURL($entry->getEntity()));
        if ($permissions->canEditExpressEntry()) {
            $this->set('editURL', $this->getEditEntryURL($entry));
        }
        if ($permissions->canDeleteExpressEntry()) {
            $this->set('allowDelete', true);
        } else {
            $this->set('allowDelete', false);
        }
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

        $renderer = \Core::make('Concrete\Core\Express\Form\StandardFormRenderer');
        $this->set('entry', $entry);
        $this->set('entity', $entry->getEntity());
        $entity = $entry->getEntity();
        $this->entityManager->refresh($entity); // sometimes this isn't eagerly loaded (?)
        $this->set('expressForm', $entity->getDefaultEditForm());
        $this->set('renderer', $renderer);
        $this->set('backURL', $this->getBackToListURL($entry->getEntity()));
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

        if ($entry === null) {
            $permissions = new \Permissions($entity);
            if (!$permissions->canAddExpressEntries()) {
                $this->error->add(t('You do not have access to add entries of this entity type.'));
            }
        } else {
            $permissions = new \Permissions($entry);
            if (!$permissions->canEditExpressEntry()) {
                $this->error->add(t('You do not have access to edit entries of this entity type.'));
            }
        }

        if ($form !== null) {
            $validator = new Validator($this->error, $this->request);
            $validator->validate($form);
            if (!$this->error->has()) {
                $manager = new Manager($this->entityManager, $this->request);
                if ($entry === null) {
                    // create
                    $entry = $manager->addEntry($entity);
                    $manager->saveEntryAttributesForm($form, $entry);
                    $this->flash(
                        'success',
                        tc(/*i18n: %s is an Express entity name*/'Express', 'New record %s added successfully.', $entity->getName())
                        .'<br />'
                        .'<a class="btn btn-default" href="'.\URL::to(\Page::getCurrentPage(), 'view_entry', $entry->getID()).'">'.t('View Record Here').'</a>',
                        true
                    );
                    $this->redirect(\URL::to(\Page::getCurrentPage(), 'create_entry', $entity->getID()));
                } else {
                    // update
                    $manager->saveEntryAttributesForm($form, $entry);
                    $this->flash('success', t('%s updated successfully.', $entity->getName()));
                    $this->redirect($this->getBackToListURL($entity));
                }
                
            }
        } else {
            throw new \Exception(t('Invalid form.'));
        }
    }
}
