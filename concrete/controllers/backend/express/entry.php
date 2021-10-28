<?php

namespace Concrete\Controller\Backend\Express;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\ExpressTransformer;
use Concrete\Core\Express\Search\Field\SiteField;
use Concrete\Core\Express\Search\SearchProvider;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Concrete\Core\Entity\Express\Entry as EntryEntity;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\ResultFactory;
use Doctrine\ORM\EntityManager;
use League\Fractal\Manager;
use League\Fractal\Pagination\PagerfantaPaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class Entry extends AbstractController
{
    protected $entityManager;

    /**
     * @var Manager
     */
    protected $manager;

    public function __construct(EntityManager $entityManager, Manager $manager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->manager = $manager->setSerializer(new DataArraySerializer());
    }

    public function getJSON()
    {
        /** @var ErrorList $errorList */
        $errorList = $this->app->make(ErrorList::class);
        $data = [];

        if ($this->request->query->has('exEntryID')) {
            $entryIDs = [];
            $entryID = $this->request->query->get('exEntryID');

            if (is_array($entryID)) {
                $entryIDs = $entryID;
            } else {
                $entryIDs[] = $entryID;
            }

            if (count($entryIDs) > 0) {
                $data = [
                    "entries" => []
                ];

                $entryRepository = $this->entityManager->getRepository(EntryEntity::class);

                foreach ($entryIDs as $entryID) {
                    /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                    $entry = $entryRepository->findOneById($entryID);

                    if ($entry instanceof EntryEntity) {
                        $permissionChecker = new Checker($entry->getEntity());
                        if ($permissionChecker->canViewExpressEntries()) {
                            $data["entries"][] = $entry->jsonSerialize();
                        } else {
                            $errorList->add(t('Access Denied.'));
                        }
                    }
                }

                if (count($data["entries"]) === 0) {
                    $errorList->add(t('Entries not found.'));
                }
            }

        } elseif ($this->request->query->has('exEntityID')) {
            $exEntityID = $this->request->query->get('exEntityID');
            $entityRepository = $this->entityManager->getRepository(Entity::class);
            $entity = $entityRepository->find($exEntityID);
            if ($entity instanceof Entity) {
                $permissionChecker = new Checker($entity);
                if ($permissionChecker->canViewExpressEntries()) {

                    $filters = [new SiteField()];
                    if ($this->request->query->has('keyword')) {
                        $filters[] = new KeywordsField($this->request->query->get('keyword'));
                    }
                    /** @var Entity $entity */
                    $entity = $entityRepository->find($exEntityID);
                    $provider = $this->app->make(SearchProvider::class, ['entity' => $entity]);
                    $resultFactory = $this->app->make(ResultFactory::class);
                    $queryFactory = $this->app->make(QueryFactory::class);
                    $query = $queryFactory->createQuery($provider, $filters);
                    $queryModifier = new QueryModifier();
                    $queryModifier->addModifier(new AutoSortColumnRequestModifier($provider, $this->request, Request::METHOD_GET));
                    $query = $queryModifier->process($query);
                    $result = $resultFactory->createFromQuery($provider, $query);
                    $list = $result->getItemListObject();
                    $data = $this->buildExpressListFractalArray($list);
                }
            }
        }

        if ($errorList->has()) {
            return new JsonResponse($errorList);
        } else {
            return new JsonResponse($data);
        }
    }

    /**
     * @param EntryList $list
     *
     * @return array
     */
    protected function buildExpressListFractalArray(EntryList $list)
    {
        $adapter = $list->getPaginationAdapter();
        $paginationFactory = $this->app->make(PaginationFactory::class, ['request' => $this->request]);
        $pagination = $paginationFactory->deliverPaginationObject($list, new Pagination($list, $adapter));
        $collection = new Collection($pagination->getCurrentPageResults(), new ExpressTransformer());
        $collection->setPaginator(new PagerfantaPaginatorAdapter($pagination, function ($page) {
            return $page;
        }));

        $collection->setMeta([
            'query_params' => [
                'pagination_page' => $list->getQueryPaginationPageParameter(),
                'sort_column' => $list->getQuerySortColumnParameter(),
                'sort_direction' => $list->getQuerySortDirectionParameter(),
            ],
        ]);

        $response = $this->manager->createData($collection);

        return $response->toArray();
    }
}
