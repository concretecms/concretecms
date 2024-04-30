<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Marketplace\PackageRepositoryInterface;
use Concrete\Core\Marketplace\PurchaseConnectionCoordinator;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Abstract controller for extending Concrete CMS through the Dashboard.
 * 
 */

abstract class MarketplaceDashboardPageController extends DashboardPageController
{
    abstract public function getRedirectLocation(): string;

    public function view(): RedirectResponse
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
        }
        return $this->buildRedirect('/dashboard/system/basics/marketplace');
    }

    /**
     * @deprecated This will be removed in version 10
     */
    public function view_detail(): void
    {
        throw new \RuntimeException('Please migrate to the new marketplace.');
    }

    /**
     * @deprecated This will be removed in version 10
     */
    public function getMarketplaceType()
    {
        throw new \RuntimeException('Please migrate to the new marketplace.');
    }

    /**
     * @deprecated This will be removed in version 10
     */
    public function getMarketplaceDefaultHeading()
    {
        throw new \RuntimeException('Please migrate to the new marketplace.');
    }
}
