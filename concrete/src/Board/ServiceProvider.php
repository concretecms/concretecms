<?php
namespace Concrete\Core\Board;

use Concrete\Core\Board\DataSource\Driver\Manager;
use Concrete\Core\Board\Instance\Slot\CollectionFactory;
use Concrete\Core\Board\Instance\Slot\Content\ContentRenderer;
use Concrete\Core\Board\Instance\Slot\ContentPopulator;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Page\Page;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Serializer;

class ServiceProvider extends Provider
{
    public function register()
    {
        $this->app->singleton(Manager::class);
        $this->app->singleton(CollectionFactory::class);

        $this->app
            ->when(ContentRenderer::class)
            ->needs(Page::class)
            ->give(function () {
                return Page::getCurrentPage();
            });

    }

}
