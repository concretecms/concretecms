<?php

namespace Concrete\Controller\Search\Express;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Entry\Search\Result\Result;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Search\Field\SiteField;
use Concrete\Core\Express\Search\SearchProvider;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\ResultFactory;
use Concrete\Core\Support\Facade\Facade;

/**
 * @TODO kill this class, this is an old class.
 * it has been modified temporarily until the new express vue component is available
 */
class Entries extends AbstractController
{
    protected $entryList;
    protected $entity;
    protected $result;
    protected $columnSet;

    public function submit($entityID)
    {
        $em = \Database::connection()->getEntityManager();
        $entity = $em->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneById($entityID);
        $this->search($entity);
        $this->app->make('helper/ajax')->sendResult($this->result->getJSONObject());
    }

    public function search(Entity $entity)
    {
        $app = Facade::getFacadeApplication();
        $provider = $app->make(SearchProvider::class, ['entity' => $entity]);
        $resultFactory = $app->make(ResultFactory::class);
        $queryFactory = $app->make(QueryFactory::class);
        $query = $queryFactory->createQuery(
            $provider,
            [
                new SiteField()
            ]
        );
        $this->result = $resultFactory->createFromQuery($provider, $query);
        $this->result->setBaseUrl(\URL::to('/ccm/system/search/express/entries/submit', $entity->getID()));
    }

    public function getSearchResultObject()
    {
        return $this->result;
    }

    public function getListObject()
    {
        return $this->entryList;
    }

}
