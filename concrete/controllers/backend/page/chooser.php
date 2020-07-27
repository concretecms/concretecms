<?php

namespace Concrete\Controller\Backend\Page;

use Concrete\Core\Http\Response;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\PageTransformer;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Response\PageResponse;
use Concrete\Core\Search\Pagination\Pagination;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class Chooser extends \Concrete\Core\Controller\Controller
{
    /**
     * @var Manager
     */
    protected $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function searchPages($keyword)
    {
        $list = new PageList();
        $list->setPermissionsChecker(function ($page) {
            /** @var PageResponse $cp */
            $cp = new Checker($page);

            return $cp->canViewPageInSitemap();
        });
        $list->filterByKeywords($keyword);
        $list->sortByPublicDateDescending();
        $list->setPageVersionToRetrieve(PageList::PAGE_VERSION_RECENT);

        $adapter = $list->getPaginationAdapter();
        $pagination = new Pagination($list, $adapter);
        $pagination->setMaxPerPage(20);
        $collection = new Collection($pagination->getCurrentPageResults(), new PageTransformer());
        $response = $this->manager->createData($collection);

        return new Response($response->toJson());
    }
}
