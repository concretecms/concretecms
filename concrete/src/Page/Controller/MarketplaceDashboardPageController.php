<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Core\Marketplace\PackageRepositoryInterface;
use Concrete\Core\Marketplace\PurchaseConnectionCoordinator;

/**
 * Abstract controller for extending Concrete CMS through the Dashboard.
 * 
 */

abstract class MarketplaceDashboardPageController extends DashboardPageController
{
    abstract public function getRedirectLocation();

    public function view()
    {
        $repository = $this->app->make(PackageRepositoryInterface::class);
        $coordinator = $this->app->make(PurchaseConnectionCoordinator::class);
        $connection = $repository->getConnection();
        if ($repository->validate($connection)) {
            // Redirect the url to the marketplace with a verified connection
            $url = $coordinator->createPurchaseConnectionUrl(
                $connection,
                $this->getRedirectLocation(),
                (string) \URL::to('/dashboard/extend'),
            );
            return $this->buildRedirect($url);
        } else {
            return $this->buildRedirect('/dashboard/system/basics/marketplace');
        }
    }
}
