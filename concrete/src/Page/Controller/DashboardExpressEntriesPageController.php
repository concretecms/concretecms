<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Core\Csv\WriterFactory;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Export\EntryList\CsvWriter;
use Concrete\Core\Express\Form\Context\DashboardFormContext;
use Concrete\Core\Express\Form\Context\DashboardViewContext;
use Concrete\Core\Express\Form\Processor\ProcessorInterface;
use Concrete\Core\Express\Form\Renderer;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Form\Context\ContextFactory;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Type\ExpressEntryResults;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class DashboardExpressEntriesPageController extends DashboardSitePageController
{

    protected function getResultsTreeNodeObject()
    {
        $tree = ExpressEntryResults::get();

        return $tree->getRootTreeNodeObject();
    }

    protected function renderList($treeNodeParentID = null)
    {
        $parent = $this->getParentNode($treeNodeParentID);

        /*
        $this->set('breadcrumb', $this->getBreadcrumb($parent));
        */
        if (isset($parent) && $parent instanceof \Concrete\Core\Tree\Node\Type\ExpressEntryResults) {
            $entity = $this->getEntity($parent);
            $permissions = new \Permissions($entity);
            if (!$permissions->canViewExpressEntries()) {
                throw new \Exception(t('Access Denied'));
            }
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

    /**
     * Export Express entries into a CSV.
     *
     * @param int|null $treeNodeParentID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function csv_export($treeNodeParentID = null)
    {
        $me = $this;
        $parent = $me->getParentNode($treeNodeParentID);
        $entity = $me->getEntity($parent);
        $permissions = new \Permissions($entity);
        if (!$permissions->canViewExpressEntries()) {
            throw new \Exception(t('Access Denied'));
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $entity->getHandle() . '.csv',
        ];
        $config = $this->app->make('config');
        $bom = $config->get('concrete.export.csv.include_bom') ? $config->get('concrete.charset_bom') : '';
        $datetime_format = $config->get('concrete.export.csv.datetime_format');

        return StreamedResponse::create(function () use ($entity, $me, $bom, $datetime_format) {
            $entryList = new EntryList($entity);

            $writer = $this->app->make(CsvWriter::class, [
                $this->app->make(WriterFactory::class)->createFromPath('php://output', 'w'),
                new Date()
            ]);
            echo $bom;
            $writer->insertHeaders($entity);
            $writer->insertEntryList($entryList,$datetime_format);
        }, 200, $headers);
    }

    /**
     * @param \Concrete\Core\Tree\Node\Type\ExpressEntryResults $parent
     *
     * @return \Concrete\Core\Entity\Express\Entity
     */
    protected function getEntity(\Concrete\Core\Tree\Node\Type\ExpressEntryResults $parent)
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneByResultsNode($parent);
    }

    /*
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

        if (1 == count($breadcrumb)) {
            array_pop($breadcrumb);
        }

        return $breadcrumb;
    }
    */



    /**
     * @param $treeNodeParentID
     */
    protected function getParentNode($treeNodeParentID)
    {
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

        return $parent;
    }
}
