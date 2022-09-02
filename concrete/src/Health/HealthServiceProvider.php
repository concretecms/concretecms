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
        $this->app->singleton(CsvWriter::class, function() {
            $config = $this->app->make('config');
            return new CsvWriter(
                $this->app->make(WriterFactory::class)->createFromPath('php://output', 'w'),
                $config
            );
        });

        // This way we can add adapters to it at runtime.
        $this->app->singleton(ScriptTagSuite::class);
    }
}
