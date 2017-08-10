<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Express\Entities;

use Concrete\Controller\Element\Search\Express\CustomizeResults;
use Concrete\Core\Express\Search\ColumnSet\ColumnSet;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\User\Search\SearchProvider;
use Concrete\Core\View\DialogView;
use Concrete\Core\View\View;

class CustomizeSearch extends DashboardPageController
{
    protected $repository;

    public function on_start()
    {
        parent::on_start();
        $this->repository = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
    }

    public function save($id = null)
    {
        $entity = $this->repository->findOneById($id);
        if (is_object($entity)) {
            if (!$this->token->validate('save')) {
                $this->error->add($this->token->getErrorMessage());
            }
            if (!$this->error->has()) {
                /**
                 * @var $provider \Concrete\Core\Express\Search\SearchProvider
                 */
                $provider = $this->app->make('Concrete\Core\Express\Search\SearchProvider', array($entity, $entity->getAttributeKeyCategory()));
                $set = $this->app->make('Concrete\Core\Express\Search\ColumnSet\ColumnSet');
                $available = $provider->getAvailableColumnSet();
                foreach ($this->request->request->get('column') as $key) {
                    $set->addColumn($available->getColumnByKey($key));
                }

                $sort = $available->getColumnByKey($this->request->request->get('fSearchDefaultSort'));
                $set->setDefaultSortColumn($sort, $this->request->request->get('fSearchDefaultSortDirection'));

                $entity->setResultColumnSet($set);
                $this->entityManager->persist($entity);
                $this->entityManager->flush();
                $this->flash('success', t('Search preferences saved successfully.'));
                $this->redirect('/dashboard/system/express/entities/customize_search', $entity->getID());
            }
            $this->view($id);
        } else {
            $this->redirect('/dashboard/system/express/entities');
        }
    }

    public function view($id = null)
    {
        $entity = $this->repository->findOneById($id);
        if (is_object($entity)) {
            $this->set('entity', $entity);
            /**
             * @var $provider SearchProvider
             */
            $provider = $this->app->make('Concrete\Core\Express\Search\SearchProvider', array($entity, $entity->getAttributeKeyCategory()));
            $element = new CustomizeResults($provider);
            $this->set('customizeElement', $element);
        } else {
            $this->redirect('/dashboard/system/express/entities');
        }
    }


}
