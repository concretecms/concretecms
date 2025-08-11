<?php
namespace Concrete\Core\Health;

use Concrete\Core\Csv\WriterFactory;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Health\Report\Finding\CsvWriter;
use Concrete\Core\Health\Report\Test\Suite\ScriptTagSuite;

class HealthServiceProvider extends ServiceProvider
{
    public function register()
    {
        // This way we can add adapters to it at runtime.
        $this->app->singleton(ScriptTagSuite::class);
    }
}
