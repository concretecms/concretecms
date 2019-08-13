<?php
namespace Concrete\Core\Export;

use Concrete\Core\Export\Item\Express\EntryStore;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

/**
 * @since 8.3.1
 */
class ExportServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(EntryStore::class);
    }
}
