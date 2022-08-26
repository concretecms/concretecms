<?php
namespace Concrete\Core\Health;

use Concrete\Core\Csv\WriterFactory;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Health\Report\Finding\CsvWriter;

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
    }
}
