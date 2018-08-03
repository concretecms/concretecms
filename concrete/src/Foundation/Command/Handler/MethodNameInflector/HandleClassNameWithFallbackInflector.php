<?php

namespace Concrete\Core\Foundation\Command\Handler\MethodNameInflector;

use League\Tactician\Handler\MethodNameInflector\ClassNameInflector;

class HandleClassNameWithFallbackInflector extends ClassNameInflector
{
    /**
     * {@inheritdoc}
     */
    public function inflect($command, $commandHandler)
    {
        $commandName = parent::inflect($command, $commandHandler);
        $method = 'handle' . ucfirst($commandName);
        if (!method_exists($commandHandler, $method)) {
            $method = 'handle';
        }
        return $method;
    }
}
