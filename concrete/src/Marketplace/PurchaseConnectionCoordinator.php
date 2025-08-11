<?php
namespace Concrete\Core\Marketplace;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\ConcreteObject;

final class PurchaseConnectionCoordinator
{

    /**
     * @var Repository
     */
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function createPurchaseConnectionUrl(ConnectionInterface $connection, string $redirectUrl, string $source): string
    {
        $url = $this->config->get('concrete.urls.marketplace')
            . $this->config->get('concrete.urls.paths.marketplace.connect')
            . '/' . $connection->getPublic() . '?redirect=' . h($redirectUrl)
            . '&source=' . h($source);
        return $url;
    }

}
