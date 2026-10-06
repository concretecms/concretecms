<?php

declare(strict_types=1);

namespace Concrete\TestHelpers\Http;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Starts (and stops) the fake HTTPS server used by the tests that need to perform real HTTPS requests.
 *
 * The server listens to a port assigned by the operating system (so that we never use a port already in use), and it
 * uses a self-signed certificate committed in the repository (see the tests/assets/Http/ssl directory).
 *
 * @see \Concrete\Tests\Http\HttpClientTest
 */
final class FakeHttpsServer
{
    /**
     * The number of seconds we wait for the server to tell us the port it listens to.
     *
     * @var float
     */
    private const STARTUP_TIMEOUT = 10.0;

    /**
     * The number of seconds we wait for the server to quit gracefully before killing it.
     *
     * @var float
     */
    private const SHUTDOWN_TIMEOUT = 3.0;

    /**
     * The currently running instance (if any).
     *
     * @var static|null
     */
    private static $instance;

    /**
     * The reason why the server couldn't be started (if that's the case).
     *
     * @var string
     */
    private static $startupError = '';

    /**
     * The process running the server.
     *
     * @var resource|null
     */
    private $process;

    /**
     * The pipes connected to the process running the server.
     *
     * @var resource[]
     */
    private $pipes = [];

    /**
     * The port the server listens to.
     *
     * @var int
     */
    private $port;

    /**
     * The file whose deletion tells the server to quit.
     *
     * @var string
     */
    private $keepaliveFile;

    /**
     * @param resource $process
     * @param resource[] $pipes
     */
    private function __construct($process, array $pipes, int $port, string $keepaliveFile)
    {
        $this->process = $process;
        $this->pipes = $pipes;
        $this->port = $port;
        $this->keepaliveFile = $keepaliveFile;
    }

    public function __destruct()
    {
        $this->stop();
    }

    /**
     * Start the server (if it's not already running).
     *
     * @throws \RuntimeException if the server can't be started
     */
    public static function start(): self
    {
        if (self::$instance === null) {
            self::$instance = self::launch();
            self::$startupError = '';
        }

        return self::$instance;
    }

    /**
     * Start the server, remembering the error (if any) instead of throwing it.
     *
     * @return bool true if the server is running, false otherwise (see getStartupError())
     */
    public static function tryStart(): bool
    {
        if (self::$instance !== null) {
            return true;
        }
        if (self::$startupError !== '') {
            return false;
        }
        try {
            self::start();
        } catch (\Throwable $x) {
            self::$startupError = $x->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Get the currently running instance (if any).
     */
    public static function getInstance(): ?self
    {
        return self::$instance;
    }

    /**
     * Get the reason why the server couldn't be started (empty string if it started fine, or if we never tried).
     */
    public static function getStartupError(): string
    {
        return self::$startupError;
    }

    /**
     * Stop the currently running instance (if any).
     */
    public static function shutdown(): void
    {
        $instance = self::$instance;
        self::$instance = null;
        if ($instance !== null) {
            $instance->stop();
        }
    }

    /**
     * Get the absolute path to the certificate authority that signed the certificate used by the server.
     *
     * Clients should use this file in order to consider the server as trusted.
     */
    public static function getCACertificateFile(): string
    {
        return self::getAssetsDirectory() . '/ssl/ca.crt';
    }

    /**
     * Get the port the server listens to.
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Get the URI of a resource served by the server.
     *
     * We use the 127.0.0.1 address (which is listed in the server certificate) instead of the "localhost" host name,
     * because on some systems "localhost" is resolved to the IPv6 ::1 address, where we don't listen to.
     */
    public function getUri(string $path = '/'): string
    {
        return 'https://127.0.0.1:' . $this->port . '/' . ltrim($path, '/');
    }

    /**
     * Is the server still running?
     */
    public function isRunning(): bool
    {
        if ($this->process === null) {
            return false;
        }
        $status = proc_get_status($this->process);

        return $status !== false && !empty($status['running']);
    }

    /**
     * Stop the server.
     */
    public function stop(): void
    {
        $process = $this->process;
        $this->process = null;
        if ($process === null) {
            return;
        }
        // Deleting the keepalive file makes the server quit gracefully
        if ($this->keepaliveFile !== '') {
            @unlink($this->keepaliveFile);
            $this->keepaliveFile = '';
        }
        $waitUntil = microtime(true) + self::SHUTDOWN_TIMEOUT;
        for (;;) {
            $status = proc_get_status($process);
            if ($status === false || empty($status['running'])) {
                break;
            }
            if (microtime(true) >= $waitUntil) {
                @proc_terminate($process);
                break;
            }
            usleep(50000);
        }
        foreach ($this->pipes as $pipe) {
            if (is_resource($pipe)) {
                @fclose($pipe);
            }
        }
        $this->pipes = [];
        @proc_close($process);
    }

    /**
     * Get the absolute path of the directory containing the server script and its certificates.
     */
    private static function getAssetsDirectory(): string
    {
        return DIR_TESTS . '/assets/Http';
    }

    /**
     * Get the php.ini settings that turn Xdebug off.
     *
     * Xdebug must not be active in the server process: at the very least it would slow it down, but it may also try to
     * connect to the very same debugging session used by the process running the tests, breaking both of them.
     *
     * @return string[]
     */
    private static function getXdebugDisablingIniSettings(): array
    {
        if (!extension_loaded('xdebug')) {
            return [];
        }
        $xdebugVersion = (string) phpversion('xdebug');
        if (version_compare($xdebugVersion, '3') >= 0) {
            return [
                'xdebug.mode=off',
                'xdebug.start_with_request=no',
            ];
        }

        return [
            'xdebug.default_enable=0',
            'xdebug.remote_enable=0',
            'xdebug.remote_autostart=0',
            'xdebug.profiler_enable=0',
        ];
    }

    /**
     * Get the environment variables to be used for the server process: they are the ones we received, but without the
     * ones that could activate Xdebug.
     *
     * @return array<string, string>
     */
    private static function getEnvironmentVariables(): array
    {
        $result = [];
        foreach (getenv() as $name => $value) {
            switch (strtoupper($name)) {
                case 'XDEBUG_CONFIG':
                case 'XDEBUG_SESSION':
                case 'XDEBUG_TRIGGER':
                    break;
                default:
                    $result[$name] = $value;
                    break;
            }
        }
        $result['XDEBUG_MODE'] = 'off';

        return $result;
    }

    /**
     * Actually start the server process.
     *
     * @throws \RuntimeException
     */
    private static function launch(): self
    {
        if (!function_exists('proc_open')) {
            throw new \RuntimeException('The proc_open PHP function is not available');
        }
        if (!extension_loaded('openssl')) {
            throw new \RuntimeException('The openssl PHP extension is not available');
        }
        $assetsDirectory = self::getAssetsDirectory();
        $keepaliveFile = @tempnam(sys_get_temp_dir(), 'ccm-fake-https-server');
        if ($keepaliveFile === false) {
            throw new \RuntimeException('Failed to create the keepalive file of the fake HTTPS server');
        }
        $keepaliveFile = str_replace(DIRECTORY_SEPARATOR, '/', $keepaliveFile);
        $arguments = [
            self::getPhpBinary(),
        ];
        foreach (self::getXdebugDisablingIniSettings() as $iniSetting) {
            $arguments[] = '-d';
            $arguments[] = $iniSetting;
        }
        $arguments = array_merge($arguments, [
            "{$assetsDirectory}/fake-https-server.php",
            "{$assetsDirectory}/ssl/server.crt",
            "{$assetsDirectory}/ssl/server.key",
            $keepaliveFile,
        ]);
        $command = implode(' ', array_map('escapeshellarg', $arguments));
        if (DIRECTORY_SEPARATOR !== '\\') {
            // Let the PHP process replace the shell spawned by proc_open, so that we can control (and kill) it directly
            $command = 'exec ' . $command;
        }
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $pipes = [];
        $process = @proc_open($command, $descriptors, $pipes, $assetsDirectory, self::getEnvironmentVariables());
        if (!is_resource($process)) {
            @unlink($keepaliveFile);

            throw new \RuntimeException('Failed to start the fake HTTPS server');
        }
        try {
            $port = self::readPort($process, $pipes);
        } catch (\Throwable $x) {
            foreach ($pipes as $pipe) {
                if (is_resource($pipe)) {
                    @fclose($pipe);
                }
            }
            @proc_terminate($process);
            @proc_close($process);
            @unlink($keepaliveFile);

            throw $x;
        }

        return new self($process, $pipes, $port, $keepaliveFile);
    }

    /**
     * Read from the standard output of the server the port it listens to.
     *
     * @param resource $process
     * @param resource[] $pipes
     *
     * @throws \RuntimeException
     */
    private static function readPort($process, array $pipes): int
    {
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);
        $output = '';
        $waitUntil = microtime(true) + self::STARTUP_TIMEOUT;
        for (;;) {
            $output .= (string) fread($pipes[1], 4096);
            if (strpos($output, "\n") !== false) {
                break;
            }
            $status = proc_get_status($process);
            if ($status === false || empty($status['running'])) {
                throw new \RuntimeException(trim('The fake HTTPS server quit unexpectedly: ' . $output . ' ' . (string) stream_get_contents($pipes[2])));
            }
            if (microtime(true) >= $waitUntil) {
                throw new \RuntimeException('Timeout while waiting for the fake HTTPS server to start');
            }
            usleep(20000);
        }
        $line = trim((string) strtok($output, "\n"));
        if (strpos($line, 'PORT:') === 0) {
            $port = (int) substr($line, strlen('PORT:'));
            if ($port > 0) {
                return $port;
            }
        }

        throw new \RuntimeException('The fake HTTPS server failed to start: ' . ($line === '' ? 'unknown error' : $line));
    }

    /**
     * Get the path to the PHP command line interpreter.
     *
     * @throws \RuntimeException
     */
    private static function getPhpBinary(): string
    {
        if (PHP_BINARY !== '' && (PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg')) {
            return PHP_BINARY;
        }
        $binary = (string) getenv('PHP_BINARY');
        if ($binary !== '' && is_file($binary)) {
            return $binary;
        }

        throw new \RuntimeException('Unable to find the PHP command line interpreter');
    }
}
