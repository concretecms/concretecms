<?php

namespace Concrete\Core\Permission\Category;

use Symfony\Component\HttpFoundation\Response;

interface TaskHandlerInterface
{
    /**
     * Entrypoint to handle the task.
     *
     * @param string $task The task to be executed
     * @param array $options Options received (for example via $_POST and/or $_GET)
     *
     * @return \Symfony\Component\HttpFoundation\Response|null Return NULL if the handler is a controller with a View: in this case return its path vith getViewPath()
     */
    public function handle(string $task, array $options): ?Response;

    /**
     * The path of the view file (used if handle returns NULL).
     *
     * @return string
     */
    public function getViewPath(): string;
}
