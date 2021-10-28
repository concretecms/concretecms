<?php

namespace Concrete\Controller\Backend\Page;

use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Http\Response;
use Concrete\Core\Page\PageTransformer;
use Concrete\Core\Page\Search\SearchProvider;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Search\Result\ResultFactory;
use League\Fractal\Manager;
use League\Fractal\Pagination\PagerfantaPaginatorAdapter;
use League\Fractal\Resource\Collection;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

class Chooser extends \Concrete\Core\Controller\Controller
{
    /**
     * @var Manager
     */
    protected $manager;


    public function __construct(Manager $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    /**
     * @return SearchProvider
     */
    protected function getSearchProvider()
    {
        return $this->app->make(SearchProvider::class);
    }

    /**
     * @return QueryFactory
     */
    protected function getQueryFactory()
    {
        return $this->app->make(QueryFactory::class);
    }

    protected function getSearchKeywordsField($keywords = null)
    {
        if (!$keywords && $this->request->query->has('keywords')) {
            $keywords = $this->request->query->get('keywords');
        }

        return new KeywordsField($keywords);
    }

    /**
     * @param Query $query
     * @return Result
     */
    protected function createSearchResult(Query $query)
    {
        $provider = $this->app->make(SearchProvider::class);
        $resultFactory = $this->app->make(ResultFactory::class);
        $queryModifier = $this->app->make(QueryModifier::class);

        $queryModifier->addModifier(new AutoSortColumnRequestModifier($provider, $this->request, Request::METHOD_GET));
        $queryModifier->addModifier(new ItemsPerPageRequestModifier($provider, $this->request, Request::METHOD_GET));
        $query = $queryModifier->process($query);

        return $resultFactory->createFromQuery($provider, $query);
    }

    /**
     * From the search results create a response that includes results and metadata
     * @param $result
     * @return Response
     */
    protected function getSearchResponse($result)
    {
        $pagination = $result->getPagination();
        $pages = $this->extractSearchResultPages($result);

        $collection = new Collection($pages, new PageTransformer());

        // Setting the paginator includes paging metadata in the result
        $this->setPaginator($collection, $pagination);

        if ($pagination->getTotalPages() === -1) {
            // Get the next and previous cursor
            $itemlist = $result->getItemListObject();
            $factory = $itemlist->getPagerVariableFactory();
            $collection->setMeta([
                'ccm' => [
                    'pagination_mode' => 'cursor',
                    'pagination_show' => $pagination->haveToPaginate(),
                    'ccm_cursor_prev' => $pagination->hasPreviousPage() ? $factory->getPreviousCursorValue($pagination) : null,
                    'ccm_cursor_next' => $pagination->hasNextPage() ? $factory->getNextCursorValue($pagination) : null,
                ],
            ]);
        } else {
            $collection->setMeta([
                'ccm' => [
                    'pagination_mode' => 'paging',
                    'pagination_show' => $pagination->haveToPaginate(),
                ],
            ]);
        }

        // Send the response to the client
        $scope = $this->manager->createData($collection);
        return new Response($scope->toJson());
    }

    /**
     * Gets an array of page objects
     * @param $result
     * @return array - List of \Concrete\Core\Page\Page
     */
    protected function extractSearchResultPages($result)
    {
        return array_map(function ($item) {
            return $item->getItem();
        }, $result->getItems());
    }

    /**
     * Add pagination to the collection adds metadata to the response
     * @param Collection $collection
     * @param Pagerfanta $pagination
     */
    protected function setPaginator(Collection $collection, Pagerfanta $pagination)
    {
        // PagerfantaPaginatorAdapter requires a Callable, which the empty function is for
        $paginatorAdapter = new PagerfantaPaginatorAdapter($pagination, function () {});
        $collection->setPaginator($paginatorAdapter);
    }

    /**
     * Callable through /ccm/system/page/chooser/search/{keywords}
     * @param $keywords
     * @return Response
     */
    public function searchPages($keywords)
    {
        $searchProvider = $this->getSearchProvider();
        $query = $this->getQueryFactory()->createQuery($searchProvider, [
            $this->getSearchKeywordsField($keywords)
        ]);

        $result = $this->createSearchResult($query);

        return $this->getSearchResponse($result);
    }
}
