<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\Category;
use Concrete\Core\Tree\Type\ExpressEntryResults;

abstract class DashboardExpressEntriesPageController extends DashboardPageController
{

    protected function getBackToListURL(Entity $entity)
    {
        return \URL::to($this->getPageObject()
            ->getCollectionPath(), 'view', $entity->getEntityResultsNodeID());
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

        $renderer = \Core::make('Concrete\Core\Express\Form\ViewRenderer');
        $this->set('entry', $entry);
        $this->set('entity', $entry->getEntity());
        $this->set('expressForm', $entry->getEntity()->getForms()[0]);
        $this->set('renderer', $renderer);
        $this->set('backURL', $this->getBackToListURL($entry->getEntity()));
        $this->render('/dashboard/express/entries/view_entry', false);
    }


}
