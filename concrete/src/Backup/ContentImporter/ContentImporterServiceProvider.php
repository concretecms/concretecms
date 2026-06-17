<?php

namespace Concrete\Core\Backup\ContentImporter;

use Concrete\Core\Application\Application;
use Concrete\Core\Backup\ContentImporter\Importer\Manager as ImporterManager;
use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;
use Concrete\Core\Export\Item\Express\EntryStore;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class ContentImporterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            'import/value_inspector/core',
            static function (Application $app): ValueInspector\ValueInspectorInterface {
                return new ValueInspector\ValueInspector();
            }
        );
        $this->app->singleton(
            'import/value_inspector',
            static function (Application $app): ValueInspector\ValueInspectorInterface {
                $inspector = $app->make('import/value_inspector/core');
                $inspector->registerInspectionRoutine(new InspectionRoutine\PageRoutine());
                $inspector->registerInspectionRoutine(new InspectionRoutine\PictureRoutine());
                $inspector->registerInspectionRoutine(new InspectionRoutine\FileRoutine());
                $inspector->registerInspectionRoutine(new InspectionRoutine\PageFeedRoutine());
                $inspector->registerInspectionRoutine(new InspectionRoutine\PageTypeRoutine());
                $inspector->registerInspectionRoutine(new InspectionRoutine\FileFolderRoutine());
                $inspector->registerInspectionRoutine(new InspectionRoutine\ImageRoutine());

                return $inspector;
            }
        );
        $this->app->alias('import/value_inspector', ValueInspector\ValueInspectorInterface::class);

        $this->app->singleton(
            'import/item/manager',
            static function (Application $app): ImporterManager {
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
