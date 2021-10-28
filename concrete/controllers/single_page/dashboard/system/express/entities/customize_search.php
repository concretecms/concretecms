<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Express\Entities;

use Concrete\Controller\Element\Search\Express\CustomizeResults;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Express\Search\SearchProvider;

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
        /**
         * @var $entity Entity
         */
        $entity = $this->repository->findOneById($id);
        if (is_object($entity)) {
            if (!$this->token->validate('save')) {
                $this->error->add($this->token->getErrorMessage());
            }
            if (!$this->error->has()) {
                /**
                 * @var $provider \Concrete\Core\Express\Search\SearchProvider
                 */
                $provider = $this->app->make(SearchProvider::class, ['entity' => $entity, 'category' => $entity->getAttributeKeyCategory()]);
                $factory = $this->app->make(QueryFactory::class);
                $query = $factory->createFromAdvancedSearchRequest($provider, $this->request);
                /**
                 * @var $query Query
                 */
                $entity->setItemsPerPage($query->getItemsPerPage());
                $entity->setResultColumnSet($query->getColumns());
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
            $provider = $this->app->make(SearchProvider::class, ['entity' => $entity, 'category' => $entity->getAttributeKeyCategory()]);
            $element = new CustomizeResults($provider);
            $this->set('customizeElement', $element);
        } else {
            $this->redirect('/dashboard/system/express/entities');
        }
    }


}
