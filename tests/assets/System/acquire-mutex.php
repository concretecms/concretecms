<?php

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\System\Mutex\MutexBusyException;

try {
    $app = Application::getFacadeApplication();
    $args = $_SERVER['argv'];
    $mutexKey = array_pop($args);
    $mutexClass = array_pop($args);
    $mutex = $app->make($mutexClass);
    try {
        $mutex->acquire($mutexKey);
        echo 'Mutex acquired';
    } catch (MutexBusyException $x) {
        echo 'Mutex busy';
    }

    return 0;
} catch (Exception $x) {
    echo $x->getMessage();

    return 1;
} catch (Throwable $x) {
    echo $x->getMessage();

    return 1;
}
