<?php

namespace Concrete\Core\File;

use Concrete\Core\Application\Application;
use Concrete\Core\File\Component\Chooser\ChooserConfiguration;
use Concrete\Core\File\Component\Chooser\ChooserConfigurationInterface;
use Concrete\Core\File\Component\Chooser\DefaultConfiguration;
use Concrete\Core\File\Component\Chooser\DefaultConfigurationFactory;
use Concrete\Core\File\Component\Chooser\Option\FileSetsOption;
use Concrete\Core\File\Component\Chooser\Option\FileUploadOption;
use Concrete\Core\File\Component\Chooser\Option\FileManagerOption;
use Concrete\Core\File\Component\Chooser\Option\SavedSearchOption;
use Concrete\Core\File\Component\Chooser\Option\SearchOption;
use Concrete\Core\File\Component\Chooser\Option\RecentUploadsOption;
use Concrete\Core\File\Import\ProcessorManager;
use Concrete\Core\File\Search\SearchProvider;
use Concrete\Core\File\Set\Set;
use Concrete\Core\File\Service\VolatileDirectory;
use Concrete\Core\File\StorageLocation\StorageLocation;
use Concrete\Core\File\StorageLocation\StorageLocationInterface;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class FileServiceProvider extends ServiceProvider
{
    public function register()
    {
        $singletons = [
            'helper/file' => '\Concrete\Core\File\Service\File',
            'helper/concrete/file' => '\Concrete\Core\File\Service\Application',
            'helper/image' => '\Concrete\Core\File\Image\BasicThumbnailer',
            'helper/mime' => '\Concrete\Core\File\Service\Mime',
            'helper/zip' => '\Concrete\Core\File\Service\Zip',
        ];

        foreach ($singletons as $key => $value) {
            $this->app->singleton($key, $value);
        }

        $this->app->singleton(\Concrete\Core\File\Image\Thumbnail\ThumbnailFormatService::class);

        $this->app->bind('image/imagick', \Imagine\Imagick\Imagine::class);
        $this->app->bind('image/gd', \Imagine\Gd\Imagine::class);
        $this->app->bind(\Imagine\Image\ImagineInterface::class, function (Application $app) {
            $config = $app->make('config');
            $libraryHandle = $config->get('concrete.file_manager.images.manipulation_library');
            switch ($libraryHandle) {
                case 'imagick':
                    $abstract = 'image/imagick';
                    break;
                case 'gd':
                default:
                    $abstract = 'image/gd';
                    break;
            }

            return $app->make($abstract);
        });
        $this->app->bind('image/thumbnailer', '\Concrete\Core\File\Image\BasicThumbnailer');

        $this->app->bind(StorageLocationInterface::class, function ($app) {
            return StorageLocation::getDefault();
        });

        $this->app->bindIf(Service\VolatileDirectory::class, function (Application $app) {
            return new VolatileDirectory(
                $app->make(\Illuminate\Filesystem\Filesystem::class),
                $app->make('helper/file')->getTemporaryDirectory()
            );
        });

        $this->app->bind(ProcessorManager::class, function (Application $app) {
            $config = $app->make('config');
            $processorManager = $app->build(ProcessorManager::class);
            foreach ($config->get('app.import_processors') as $processorClass) {
                if ($processorClass) {
                    $processor = $app->make($processorClass);
                    $processorManager->registerProcessor($processor->readConfiguration($config));
                }
            }

            return $processorManager;
        });

        $this->app->singleton(ChooserConfigurationInterface::class, function($app) {
            return $this->app->make(DefaultConfigurationFactory::class)->createConfiguration();
        });

        $this->app->bindIf(Upload\ClientSideUploader::class, Upload\Dropzone::class);
    }
}
