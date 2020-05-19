<?php

namespace Concrete\TestHelpers;

trait DockerTrait
{
    /**
     * Check that Docker is available and that we can pull a specific docker image.
     *
     * @param string $imageName
     *
     * @throws \PHPUnit\Framework\SkippedTestError
     */
    protected static function ensureDockerImage($imageName)
    {
        $output = [];
        $rc = -1;
        exec('docker --version 2>&1', $output, $rc);
        if ($rc !== 0) {
            self::markTestSkipped("This test requires Docker, but it's not available");
        }
        exec('docker images ' . escapeshellarg($imageName) . ' --format ' . escapeshellarg('{{.ID}}') . '  2>&1', $output, $rc);
        if ($rc !== 0) {
            self::markTestSkipped("This test requires Docker, but it's not running");
        }
        if (trim(implode("\n", $output)) !== '') {
            return;
        }
        exec('docker pull --quiet ' . escapeshellarg($imageName) . ' 2>&1', $output, $rc);
        if ($rc !== 0) {
            self::markTestSkipped("Failed to fetch Docker image \"{$imageName}\": " . trim(implode("\n", $output)));
        }
    }

    /**
     * Check that Docker is available and that we can pull a specific docker image.
     *
     * @param string $imageName
     * @param string $script
     * @param string $phpVersion
     *
     * @throws \PHPUnit\Framework\SkippedTestError
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    protected function runScriptInDocker($imageName, $script, $phpVersion = '7.2')
    {
        self::ensureDockerImage($imageName);
        $cmd = implode(' ', [
            // Run a docker container
            'docker run',
            // Destroy the container when it's done
            '--rm',
            // Mount the current concrete directory
            '-v ' . escapeshellarg(str_replace('/', DIRECTORY_SEPARATOR, DIR_BASE . '/concrete') . ':/app/concrete'),
            // Mount the test directory
            '-v ' . escapeshellarg(str_replace('/', DIRECTORY_SEPARATOR, DIR_BASE . '/tests/assets/Docker') . ':/app-test'),
            // Set the CCM_TEST_PHPVERSION environment variable
            '-e ' . escapeshellarg("CCM_TEST_PHPVERSION={$phpVersion}"),
            // Override the defaul entry point
            '--entrypoint ""',
            // The base container image
            escapeshellarg($imageName),
            // The script to be executed
            "/app-test/{$script}",
            // Capture the standard error too
            '2>&1',
        ]);
        $output = [];
        $rc = -1;
        exec($cmd, $output, $rc);
        $this->assertSame(0, $rc, sprintf("The command '%s' failed:\n%s\n\ndocker command: %s", $script, trim(implode("\n", $output)), $cmd));
    }
}
