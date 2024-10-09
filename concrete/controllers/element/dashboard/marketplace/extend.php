<?php

namespace Concrete\Controller\Element\Dashboard\Marketplace;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Marketplace\Connection;
use Concrete\Core\Marketplace\PackageRepositoryInterface;
use Concrete\Core\Marketplace\PurchaseConnectionCoordinator;
use Concrete\Core\Url\Resolver\PathUrlResolver;
use Concrete\Core\Url\Resolver\UrlResolverInterface;
use Concrete\Core\Marketplace\ConnectionInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Extend extends ElementController
{

    /**
     * @var PackageRepositoryInterface
     */
    protected $packageRepository;

    /**
     * @var PurchaseConnectionCoordinator
     */
    protected $purchaseConnectionCoordinator;

    /**
     * @var UrlResolverInterface
     */
    protected $urlResolver;

    public function __construct(
        PackageRepositoryInterface $packageRepository,
        PurchaseConnectionCoordinator $purchaseConnectionCoordinator,
        PathUrlResolver $urlResolver
    ) {
        $this->packageRepository = $packageRepository;
        $this->purchaseConnectionCoordinator = $purchaseConnectionCoordinator;
        $this->urlResolver = $urlResolver;
        parent::__construct();
    }


    /**
     * @return string
     */
    public function getElement()
    {
        return 'dashboard/marketplace/extend';
    }

    private function getPurchaseConnectionUrl(Connection $connection, string $url): string
    {
        return $this->purchaseConnectionCoordinator->createPurchaseConnectionUrl(
            $connection,
            $url,
            (string) $this->urlResolver->resolve(['/dashboard/extend'])
        );
    }

    public function view()
    {
        $connection = $this->packageRepository->getConnection();
        if ($connection instanceof ConnectionInterface && $this->packageRepository->validate($connection)) {
            $this->set('browseThemesUrl', $this->getPurchaseConnectionUrl($connection, '/themes'));
            $this->set('browseAddonsUrl', $this->getPurchaseConnectionUrl($connection, '/addons'));
            $this->set('browseIntegrationsUrl', $this->getPurchaseConnectionUrl($connection, '/integrations'));
        }
    }

}
