<?php
namespace Concrete\Core\Search\Pagination;

use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\PermissionableListItemInterface;
use Pagerfanta\Exception\LessThan1CurrentPageException;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

class PaginationFactory
{

    const PERMISSIONED_PAGINATION_STYLE_FULL = 1; // Caps results at 1000, shows full paging numbers; slower and limited but nicer
    const PERMISSIONED_PAGINATION_STYLE_PAGER = 2; // No permissioned pagination cap, shows next/back; faster but limited UI

    /**
     * @var Request
     */
    protected $request;

    /**
     * PaginationFactory constructor.
     * @param PaginationProviderInterface $itemList
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function canUseSimplePagination(PaginationProviderInterface $itemList)
    {
        if ($itemList instanceof PermissionableListItemInterface) {
            if ($itemList->getPermissionsChecker() === -1) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * @param ItemList $itemList
     * @return Pagerfanta
     */
    public function createPaginationObject($itemList, $permissionedStylePagination = self::PERMISSIONED_PAGINATION_STYLE_FULL)
    {
        if ($itemList instanceof PaginationProviderInterface) {
            $canUseSimplePagination = true;
            if ($itemList instanceof PermissionableListItemInterface) {
                $canUseSimplePagination = $this->canUseSimplePagination($itemList);
            }

            if ($canUseSimplePagination) {
                // Simple pagination is always best, so it isn't an option in the method.
                $adapter = $itemList->getPaginationAdapter();
                $pagination = new Pagination($itemList, $adapter);
            } else {
                if ($permissionedStylePagination == self::PERMISSIONED_PAGINATION_STYLE_PAGER && $itemList instanceof PagerProviderInterface) {
                    $pagination = new PagerPagination($itemList);
                } else {
                    $pagination = new PermissionablePagination($itemList);
                }
            }

            return $this->deliverPaginationObject($itemList, $pagination);
        } else {
            return $itemList->getPagination();
        }
    }

    /**
     * @param ItemList $itemList
     * @param Pagerfanta $pagination
     * @return Pagerfanta
     */
    public function deliverPaginationObject(ItemList $itemList, Pagerfanta $pagination)
    {
        if ($itemList->getItemsPerPage() > -1) {
            $pagination->setMaxPerPage($itemList->getItemsPerPage());
        }
        $query = $this->request->query;
        if ($query->has($itemList->getQueryPaginationPageParameter())) {
            $page = intval($query->get($itemList->getQueryPaginationPageParameter()));
            try {
                $pagination->setCurrentPage($page);
            } catch (LessThan1CurrentPageException $e) {
                $pagination->setCurrentPage(1);
            } catch (OutOfRangeCurrentPageException $e) {
                $pagination->setCurrentPage(1);
            }
        }
        return $pagination;
    }


}
