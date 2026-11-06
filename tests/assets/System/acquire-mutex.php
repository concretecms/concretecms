<?php

use Concrete\Core\System\Mutex\MutexBusyException;

// Launched by MutexTest to check that a mutex acquired by another process is seen as busy

require __DIR__ . '/../../bootstrap.php';

$args = $_SERVER['argv'];
$mutexKey = array_pop($args);
$mutexClass = array_pop($args);
$mutex = app($mutexClass);
try {
    $mutex->acquire($mutexKey);
    echo 'Mutex acquired';
} catch (MutexBusyException $x) {
    echo 'Mutex busy';
}
