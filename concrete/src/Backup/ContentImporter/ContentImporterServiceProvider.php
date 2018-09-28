<?php
namespace Concrete\Core\Backup\ContentImporter;

use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\FileRoutine;
use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\ImageRoutine;
use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\PageFeedRoutine;
use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\PageRoutine;
use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\PageTypeRoutine;
use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\PictureRoutine;
use Concrete\Core\Backup\ContentImporter\ValueInspector\ValueInspector;
use Concrete\Core\Export\Item\Express\EntryStore;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Backup\ContentImporter\Importer\Manager as ImporterManager;

class ContentImporterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            'import/value_inspector/core',
            function ($app) {
                $inspector = new ValueInspector();

                return $inspector;
            }
        );

        $this->app->bindshared(
            'import/value_inspector',
            function ($app) {
                /*
                 * @var \Concrete\Core\Backup\ContentImporter\ValueInspector\ValueInspector
                 */
                $inspector = $app->make('import/value_inspector/core');
                $inspector->registerInspectionRoutine(new PageRoutine());
                $inspector->registerInspectionRoutine(new PictureRoutine());
                $inspector->registerInspectionRoutine(new FileRoutine());
                $inspector->registerInspectionRoutine(new PageFeedRoutine());
                $inspector->registerInspectionRoutine(new PageTypeRoutine());
                $inspector->registerInspectionRoutine(new ImageRoutine());

                return $inspector;
            }
        );

        $this->app->bindshared(
            'import/item/manager',
            function ($app) {
                $importer = $app->make(ImporterManager::class);
                foreach($app->make('config')->get('app.importer_routines') as $routine) {
                    $importer->registerImporterRoutine($app->make($routine));
                }
                return $importer;
            }
        );

        $this->app->singleton(EntryStore::class);

    }
}
