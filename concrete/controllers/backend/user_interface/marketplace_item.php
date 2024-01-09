<?php
namespace Concrete\Controller\Backend\UserInterface;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Legacy\TaskPermission;
use Concrete\Core\Marketplace\Marketplace;
use Concrete\Core\Marketplace\RemoteItem;

/**
 * @deprecated This will be removed in version 10
 */
abstract class MarketplaceItem extends UserInterface
{
    protected $marketplace;
    protected $item;

    public function view()
    {
        return $this->buildRedirect('/dashboard/extend/connect');
    }

    protected function canAccess()
    {
        return true;
    }
}
