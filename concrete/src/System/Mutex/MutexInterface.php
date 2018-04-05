<?php

namespace Concrete\Core\System\Mutex;

use Concrete\Core\Application\Application;

/**
 * Represent a class that can offer a mutually-exclusive system that allows, for instance, to be sure to run some code just once even in concurrent situations.
 *
 * @example
 * <pre>
 * try {
 *    $app->make(MutexInterface::class)->execute('my-mutex', function() {
 *        // This code won't be executed by two
 *    });
 * } catch (MutexBusyException $x) {
 *     // Another process is already using the 'my-mutex' key
 * }
 * </pre>
 * @example
 * <pre>
 * $mutex = $app->make(MutexInterface::class);
 * try {
 *    $mutex->acquire('my-mutex');
 *    // This code won't be executed by two
 *    $mutex->release('my-mutex');
 * } catch (MutexBusyException $x) {
 *     // Another process is already using the 'my-mutex' key
 * }
 * </pre>
 */
interface MutexInterface
{
    /**
     * Is this mutex available for the current system?
     *
     * @param Application $app
     *
     * @return bool
     */
    public static function isSupported(Application $app);

    /**
     * Acquire a mutex given its key.
     * If the mutex is already acquired (for example by another process), a.
     *
     * @param string $key The identifier of the mutex you want to acquire
     *
     * @throws InvalidMutexKeyException Throws an InvalidMutexKeyException exception if the mutex key is not listed in the app.mutex configuration key
     * @throws MutexBusyException Throws a MutexBusyException exception if the mutex key is already acquired
     */
    public function acquire($key);

    /**
     * Release a mutex given its key.
     * If the mutex not already acquired nothing happens.
     * When the current PHP process ends, the mutex will be released automatically.
     *
     * @param string $key The identifier of the mutex you want to release
     */
    public function release($key);

    /**
     * Execute a callable by acquiring and releasing a mutex, so that the callable won't be executed by multiple processes concurrently.
     *
     * @param string $key The identifier of the mutex
     * @param callable $callback The callback function to be executed
     *
     * @throws InvalidMutexKeyException Throws an InvalidMutexKeyException exception if the mutex key is not listed in the app.mutex configuration key
     * @throws MutexBusyException Throws a MutexBusyException exception if the mutex key is already acquired
     */
    public function execute($key, callable $callback);
}
