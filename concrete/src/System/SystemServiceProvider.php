<?php

namespace Concrete\Core\System;

use Concrete\Core\Application\Application;
use Concrete\Core\File\Service\File as FileService;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class SystemServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Mutex\MutexInterface::class, function (Application $app) {
            if (Mutex\SemaphoreMutex::isSupported($app)) {
                $mutexClass = Mutex\SemaphoreMutex::class;
            } else {
                $mutexClass = Mutex\FileLockMutex::class;
            }

            return $app->make($mutexClass);
        });
        $this->app
            ->when(Mutex\SemaphoreMutex::class)
            ->needs('$temporaryDirectory')
            ->give(function (Application $app) {
                return $app->make(FileService::class)->getTemporaryDirectory();
            })
        ;
        $this->app
            ->when(Mutex\FileLockMutex::class)
            ->needs('$temporaryDirectory')
            ->give(function (Application $app) {
                return $app->make(FileService::class)->getTemporaryDirectory();
            })
        ;
    }
}
