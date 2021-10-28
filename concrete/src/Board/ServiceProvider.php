<?php
namespace Concrete\Core\Board;

use Concrete\Core\Board\DataSource\Driver\Manager;
use Concrete\Core\Board\Instance\Slot\CollectionFactory;
use Concrete\Core\Board\Instance\Slot\Content\ContentRenderer;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Page\Page;
use Concrete\Core\Board\Template\Driver\Manager as BoardTemplateManager;
use Concrete\Core\Board\Template\Slot\Driver\Manager as BoardSlotTempateManager;

class ServiceProvider extends Provider
{
    public function register()
    {
        $this->app->singleton(BoardTemplateManager::class);
        $this->app->singleton(BoardSlotTempateManager::class);
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
