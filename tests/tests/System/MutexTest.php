<?php

namespace Concrete\Tests\Update;

use Concrete\Core\Application\Application;
use Concrete\Core\Support\Facade\Application as ApplicationFacade;
use Concrete\Core\System\Mutex\FileLockMutex;
use Concrete\Core\System\Mutex\MutexBusyException;
use Concrete\Core\System\Mutex\SemaphoreMutex;
use PHPUnit_Framework_TestCase;

class MutexTest extends PHPUnit_Framework_TestCase
{
    public function mutexProvider()
    {
        $app = ApplicationFacade::getFacadeApplication();

        return [
            [$app, SemaphoreMutex::class],
            [$app, FileLockMutex::class],
        ];
    }

    /**
     * @dataProvider mutexProvider
     *
     * @param string $mutexClass
     * @param Application $app
     */
    public function testMutex(Application $app, $mutexClass)
    {
        $isSupported = $mutexClass . '::isSupported';
        if (call_user_func($isSupported, $app) !== true) {
            $this->markTestSkipped($mutexClass . ' mutex is not supported');
        }
        $mutex = $app->make($mutexClass);
        /* @var \Concrete\Core\System\Mutex\MutexInterface $mutex */
        $key1 = 'ccm-test-mutex-1-' . mt_rand(0, mt_getrandmax());
        $key2 = 'ccm-test-mutex-2-' . mt_rand(0, mt_getrandmax());
        try {
            // Let's check that the mutex works when there's no concurrency.
            $executed = false;
            $mutex->execute($key1, function () use (&$executed) { $executed = true; });
            $this->assertTrue($executed);

            // Let's acquire two mutexes
            $mutex->acquire($key1);
            $mutex->acquire($key2);

            // Let's be sure that acquiring again these mutex fails
            $error = null;
            try {
                $mutex->acquire($key1);
            } catch (MutexBusyException $x) {
                $error = $x;
            }
            $this->assertInstanceOf(MutexBusyException::class, $error);
            $error = null;
            try {
                $mutex->execute($key2, function () {});
            } catch (MutexBusyException $x) {
                $error = $x;
            }
            $this->assertInstanceOf(MutexBusyException::class, $error);

            // Let's launch another process, so that we can check that the mutex system works across different processes
            $mutex->release($key2);
            foreach ([$key1 => 'Mutex busy', $key2 => 'Mutex acquired'] as $key => $result) {
                $cmd = escapeshellarg(PHP_BINARY);
                if (PHP_SAPI === 'phpdbg') {
                    $cmd .= ' -qrr';
                }
                $cmd .= ' ' . escapeshellarg(str_replace('/', DIRECTORY_SEPARATOR, DIR_BASE_CORE . '/bin/concrete5'));
                $cmd .= ' c5:exec';
                $cmd .= ' --no-interaction --ansi';
                $cmd .= ' ' . escapeshellarg(str_replace('/', DIRECTORY_SEPARATOR, DIR_TESTS . '/assets/System/acquire-mutex.php'));
                $cmd .= ' ' . escapeshellarg(get_class($mutex));
                $cmd .= ' ' . escapeshellarg($key);
                $cmd .= ' 2>&1';
                $rc = -1;
                $output = [];
                @exec($cmd, $output, $rc);
                if ($rc !== 0) {
                    $this->markTestSkipped('Failed to launch PHP to check mutex (' . implode("\n", $output) . ')');
                }
                $this->assertSame($result, $output[0]);
            }
        } finally {
            $mutex->release($key2);
            $mutex->release($key1);
        }
    }
}
