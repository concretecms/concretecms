<?php
namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Foundation\Command\Handler\MethodNameInflector\HandleClassNameWithFallbackInflector;
use Concrete\Core\Foundation\Command\Middleware\BatchUpdatingMiddleware;
use Illuminate\Config\Repository;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;

class SynchronousBus extends AbstractSynchronousBus
{

    public static function getHandle()
    {
        return 'core_sync';
    }

}
