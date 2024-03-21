<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Core\Marketplace\PackageRepositoryInterface;

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
        $connection = $repository->getConnection();
        if ($repository->validate($connection)) {
            // Redirect the url to the marketplace with a verified connection
            $config = $this->app->make('config');
            $url = $config->get('concrete.urls.marketplace')
                . $config->get('concrete.urls.paths.marketplace.connect')
                . '/' . $connection->getPublic() . '?redirect=' . h($this->getRedirectLocation());
            return $this->buildRedirect($url);
        } else {
            return $this->buildRedirect('/dashboard/extend/connect');
        }
    }
}
