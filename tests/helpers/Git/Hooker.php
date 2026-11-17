<?php

declare(strict_types=1);

namespace Concrete\TestHelpers\Git;

use Composer\Factory;
use Composer\Script\Event;

final class Hooker
{
    public static function installHooks(Event $e): void
    {
        try {
            $hooksDir = self::getHooksDir();
            if ($hooksDir === '') {
                throw new \RuntimeException('Unable to find the git hooks directory.');
            }
            if (!is_writable($hooksDir)) {
                throw new \RuntimeException("The directory {$hooksDir} is not writable.");
            }
            self::installPrePushHook($e, $hooksDir);
        } catch (\RuntimeException $x) {
            $e->getIO()->error($x->getMessage());
        } catch (\Throwable $x) {
            $e->getIO()->error((string) $x);
        }
    }

    private static function installPrePushHook(Event $e, string $hooksDir): void
    {
        $hook = <<<'EOT'
        #!/bin/sh
        set -o nounset
        set -o errexit
        if [ -e ./tests/helpers/Git/hooks/pre-push.sh ]; then
            sh ./tests/helpers/Git/hooks/pre-push.sh
        fi
        EOT;
        $hook = str_replace(["\r\n", "\r"], "\n", $hook) . "\n";
        $filename = $hooksDir . '/pre-push';
        if (is_file($filename) && file_get_contents($filename) === $hook) {
            $e->getIO()->info('pre-push hook is already up-to-date');
        } elseif (file_put_contents($filename, $hook) === false) {
            throw new \RuntimeException('Failed to write the pre-push hook');
        } else {
            $e->getIO()->notice('pre-push hook has been installed/updated');
        }
        if (DIRECTORY_SEPARATOR !== '\\') {
            @chmod($filename, 0755);
        }
    }

    private static function getHooksDir(): string
    {
        $composerJsonFile = Factory::getComposerFile();
        $realComposerJsonFile = $composerJsonFile ? realpath($composerJsonFile) : false;
        if ($realComposerJsonFile === false) {
            return '';
        }
        $projectRootDir = dirname($realComposerJsonFile);
        $output = [];
        $rc = -1;
        exec('git -C ' . escapeshellarg(str_replace('/', DIRECTORY_SEPARATOR, $projectRootDir)) . ' rev-parse --git-path hooks 2>' . (DIRECTORY_SEPARATOR === '\\' ? 'NUL' : '/dev/null'), $output, $rc);
        if ($rc !== 0 || empty($output[0])) {
            return '';
        }
        $hooksDir = $output[0];
        if (DIRECTORY_SEPARATOR === '\\') {
            if (!preg_match('{^[A-Za-z]:[/\\\\]}', $hooksDir)) {
                $hooksDir = $projectRootDir . '/' . $hooksDir;
            }
        } else {
            if ($hooksDir[0] !== '/') {
                $hooksDir = $projectRootDir . '/' . $hooksDir;
            }
        }
        $hooksDir = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $hooksDir), '/');
        if (!is_dir($hooksDir) && !@mkdir($hooksDir, 0755, true)) {
            return '';
        }

        return $hooksDir;
    }
}
