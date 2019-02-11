<?php

namespace Concrete\Core\System;

use Concrete\Core\Application\Application;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\Service\File as FileService;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class SystemServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Mutex\MutexInterface::class, function (Application $app) {
            $config = $app->make('config');
            $list = $config->get('concrete.mutex') ?: [];
            $list = array_filter($list, 'is_array');
            usort($list, function (array $a, array $b) {
                $priorityA = isset($a['priority']) ? (int) $a['priority'] : 0;
                $priorityB = isset($b['priority']) ? (int) $b['priority'] : 0;

                return $priorityB - $priorityA;
            });
            foreach ($list as $item) {
                $class = isset($item['class']) ? (string) $item['class'] : '';
                if ($class === '') {
                    continue;
                }
                if (!class_exists($class)) {
                    continue;
                }
                if (!$class::isSupported($app)) {
                    continue;
                }

                return $app->make($class);
            }
            $err = new UserMessageException(t("There's no available mutex class."));
            $err->setCanBeLogged(true);
            throw $err;
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
