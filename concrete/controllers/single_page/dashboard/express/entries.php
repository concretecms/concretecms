<?php
namespace Concrete\Controller\SinglePage\Dashboard\Express;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Csv\WriterFactory;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Entity\Search\SavedExpressSearch;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Export\EntryList\CsvWriter;
use Concrete\Core\Express\Form\Context\DashboardFormContext;
use Concrete\Core\Express\Form\Context\DashboardViewContext;
use Concrete\Core\Express\Form\OwnedEntityForm;
use Concrete\Core\Express\Form\Processor\ProcessorInterface;
use Concrete\Core\Express\Form\Renderer;
use Concrete\Core\Express\Search\Field\SiteField;
use Concrete\Core\Express\Search\SearchProvider;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Form\Context\ContextFactory;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Search\Result\ResultFactory;
use Concrete\Core\Tree\Node\Node;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Entries extends DashboardSitePageController
{

    /**
     * @var Element
     */
    protected $headerMenu;

    /**
     * @var Element
     */
    protected $headerSearch;

    /**
     * @return SearchProvider
     */
    protected function getSearchProvider(Entity $entity)
    {
        $category = $this->app->make(ExpressCategory::class, ['entity' => $entity]);
        return $this->app->make(SearchProvider::class, [
            'entity' => $entity, 'category' => $category
        ]);
    }

    /**
     * @return QueryFactory
     */
    protected function getQueryFactory()
    {
        return $this->app->make(QueryFactory::class);
    }

    /**
     * @return DashboardFileManagerBreadcrumbFactory
     */
    /*
    protected function createBreadcrumbFactory()
    {
        return $this->app->make(DashboardFileManagerBreadcrumbFactory::class);
    }*/

    protected function getHeaderMenu()
    {
        if (!isset($this->headerMenu)) {
            $this->headerMenu = $this->app->make(ElementManager::class)->get('express/search/menu');
        }
        return $this->headerMenu;
    }

    protected function getHeaderSearch()
    {
        if (!isset($this->headerSearch)) {
            $this->headerSearch = $this->app->make(ElementManager::class)->get('express/search/search');
        }
        return $this->headerSearch;
    }

    /**
     * @param Result $result
     */
    protected function renderSearchResult(Result $result)
    {
        $entity = $result->getEntity();
        $query = $result->getQuery();
        $headerMenu = $this->getHeaderMenu();
        $headerSearch = $this->getHeaderSearch();
        $headerMenu->getElementController()->setQuery($query);
        $headerMenu->getElementController()->setEntity($entity);
        $headerSearch->getElementController()->setQuery($query);
        $headerSearch->getElementController()->setEntity($entity);

        $permissions = new Checker($entity);

        if ($permissions->canAddExpressEntries()) {
            $headerMenu->getElementController()->setCreateURL(
                $this->app->make('url/resolver/path')->resolve([
                    $this->getPageObject()->getCollectionPath(), 'create_entry', $entity->getID()])
            );
        }

        $headerMenu->getElementController()->setExportURL(
            $this->app->make('url/resolver/path')->resolve([
                $this->getPageObject()->getCollectionPath(), 'csv_export', $entity->getEntityResultsNodeId()])
        );

        $this->set('result', $result);
        $this->set('headerMenu', $headerMenu);
        $this->set('headerSearch', $headerSearch);
        $this->setThemeViewTemplate('full.php');
        $this->render('/dashboard/express/entries/entries', false);
    }

    /**
     * @param Query $query
     * @return Result
     */
    protected function createSearchResult(Entity $entity, Query $query)
    {
        $provider = $this->getSearchProvider($entity);
        $resultFactory = $this->app->make(ResultFactory::class);
        $queryModifier = $this->app->make(QueryModifier::class);

        $queryModifier->addModifier(new AutoSortColumnRequestModifier($provider, $this->request, Request::METHOD_GET));
        $queryModifier->addModifier(new ItemsPerPageRequestModifier($provider, $this->request, Request::METHOD_GET));
        $query = $queryModifier->process($query);

        return $resultFactory->createFromQuery($provider, $query);
    }

    protected function getSearchKeywordsField()
    {
        $keywords = null;
        if ($this->request->query->has('keywords')) {
            $keywords = $this->request->query->get('keywords');
            return new KeywordsField($keywords);
        }
    }

    public function view($entity = null, $folder = null)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        if ($entity) {
            $entity = $r->findOneById($entity);
        }
        if (isset($entity) && is_object($entity)) {
            $this->set('entity', $entity);
            $fields = [
                new SiteField()
            ];
            $keywordsField = $this->getSearchKeywordsField();
            if ($keywordsField) {
                $fields[] = $keywordsField;
            }

            $query = $this->getQueryFactory()->createQuery($this->getSearchProvider($entity), $fields);
            $result = $this->createSearchResult($entity, $query);
            $this->set('pageTitle', tc(/*i18n: %s is an entity name*/'EntriesOfEntityName', '%s Entries', $entity->getName()));
            $this->renderSearchResult($result);
        } else {
            $this->set('pageTitle', t('View Express Entities'));
            $this->set('entities', $r->findPublicEntities());
        }
    }

    public function advanced_search($entity)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        if ($entity) {
            $entity = $r->findOneById($entity);
        }
        if ($entity) {
            $query = $this->getQueryFactory()->createFromAdvancedSearchRequest(
                $this->getSearchProvider($entity), $this->request, Request::METHOD_GET
            );
            $result = $this->createSearchResult($entity, $query);
            $this->renderSearchResult($result);
        }
    }

    public function preset($presetID = null)
    {
        /**
         * @TODO - this is currently broken
         */
        throw new \Exception(t('Not implemented yet.'));
        if ($presetID) {
            $preset = $this->entityManager->find(SavedExpressSearch::class, $presetID);
            if ($preset) {
                $query = $this->getQueryFactory()->createFromSavedSearch($preset);
                $result = $this->createSearchResult($query);
                $this->renderSearchResult($result);

                $factory = $this->createBreadcrumbFactory();
                $this->setBreadcrumb($factory->getBreadcrumb($this->getPageObject(), $preset));

                return;
            }
        }
        $this->view();
    }

    // formerly entity page controller
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
        $this->set('pageTitle', t('Add %s', $entity->getName()));
        $this->render('/dashboard/express/entries/create', false);
    }

    // these methods from the entries page conotroller

    /**
     * Export Express entries into a CSV.
     *
     * @param int|null $treeNodeParentID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function csv_export($resultsNodeID = null)
    {
        $me = $this;
        $node = Node::getByID($resultsNodeID);
        $r = $this->entityManager->getRepository(Entity::class);
        $entity = $r->findOneByResultsNode($node);
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
            $entryList->filterBySite($this->getSite());

            $writer = $this->app->make(CsvWriter::class, [
                $this->app->make(WriterFactory::class)->createFromPath('php://output', 'w'),
                new Date()
            ]);
            echo $bom;
            $writer->insertHeaders($entity);
            $writer->insertEntryList($entryList,$datetime_format);
        }, 200, $headers);
    }

    protected function getBackURL(Entity $entity)
    {
        return \URL::to($this->getPageObject()
            ->getCollectionPath(), 'view', $entity->getEntityResultsNodeID());
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
        $this->set('subEntities', $subEntities);
        $this->set('pageTitle', t('View %s Entry', $entity->getName()));
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

        $this->set('renderer', $renderer);
        $this->set('backURL', $this->getBackURL($entry->getEntity()));
        $this->set('pageTitle', t('Edit %s Entry', $entity->getName()));
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
                $validator->validate($form, ProcessorInterface::REQUEST_TYPE_ADD);
            } else {
                $validator->validate($form, ProcessorInterface::REQUEST_TYPE_UPDATE);
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
                        . '<br />'
                        . '<a class="btn btn-default" href="' . \URL::to(\Page::getCurrentPage(), 'view_entry', $entry->getID()) . '">' . t('View Record Here') . '</a>',
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
                    $this->redirect($this->getBackURL($entity));
                }
            }
        } else {
            throw new \Exception(t('Invalid form.'));
        }
    }

    /**
     * @var $entity Entity
     */
    /*
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



*/
}
