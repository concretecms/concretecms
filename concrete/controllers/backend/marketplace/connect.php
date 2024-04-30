<?php
namespace Concrete\Controller\Backend\Marketplace;

use Concrete\Controller\Backend\UserInterface\MarketplaceItem;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Legacy\TaskPermission;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @deprecated This will be removed in version 10
 */
class Connect extends MarketplaceItem
{
    public function view()
    {
        $errorList = new ErrorList();
        $errorList->add('Please migrate to the new marketplace.');
        return new JsonResponse($errorList, 400);
    }

    protected function canAccess()
    {
        return false;
    }
}
