<?php

namespace Concrete\Core\Package {
    if (!function_exists(__NAMESPACE__ . '\rename')) {
        function rename($old, $new)
        {
            if (!empty($GLOBALS['PACKAGE_BACKUP_TEST_FORCE_RENAME_FAILURE'])) {
                return false;
            }

            return \rename($old, $new);
        }
    }
}

namespace Concrete\Tests\Package {
    use Concrete\Core\Application\Application;
    use Concrete\Core\Error\ErrorList\ErrorList;
    use Concrete\Core\File\Service\File;
    use Concrete\Core\Package\Package;
    use Concrete\Tests\TestCase;

    class PackageBackupTest extends TestCase
    {
        public function testBackupReportsPackageDirectoryNotWritable(): void
        {
            if (DIRECTORY_SEPARATOR === '\\') {
                $this->markTestSkipped('Changing directory writability is not reliable on Windows.');
            }

            $handle = 'package_backup_not_writable_test';
            $packagePath = DIR_PACKAGES . '/' . $handle;
            $trash = app('config')->get('concrete.misc.package_backup_directory');
            $createdTrash = false;

            if (is_dir($packagePath)) {
                app(File::class)->removeAll($packagePath, true);
            }
            if (!is_dir($packagePath) && !@mkdir($packagePath, 0777, true)) {
                $this->markTestSkipped('Unable to create the test package directory.');
            }
            if (!is_dir($trash)) {
                $createdTrash = @mkdir($trash, app('config')->get('concrete.filesystem.permissions.directory'), true);
            }
            if (!is_dir($trash) || !is_writable($trash)) {
                $this->markTestSkipped('Package trash directory is not writable in this environment.');
            }

            $originalPermissions = fileperms(DIR_PACKAGES) & 0777;
            $result = null;
            $package = new class(app(), $handle) extends Package {
                protected $pkgHandle;

                public function __construct(Application $app, string $handle)
                {
                    parent::__construct($app);
                    $this->pkgHandle = $handle;
                }
            };

            try {
                if (!@chmod(DIR_PACKAGES, 0555)) {
                    $this->markTestSkipped('Unable to change package directory permissions.');
                }

                clearstatcache(true, DIR_PACKAGES);
                if (is_writable(DIR_PACKAGES)) {
                    $this->markTestSkipped('The current process can still write to the package directory.');
                }

                $result = $package->backup();
            } finally {
                @chmod(DIR_PACKAGES, $originalPermissions);
                if (is_dir($packagePath)) {
                    app(File::class)->removeAll($packagePath, true);
                }
                if ($createdTrash && is_dir($trash)) {
                    @rmdir($trash);
                }
            }

            $this->assertInstanceOf(ErrorList::class, $result);
            $message = (string) $result;
            $this->assertStringContainsString('Unable to move the package directory because the package installation directory', $message);
            $this->assertStringContainsString(DIR_PACKAGES, $message);
            $this->assertStringContainsString('not writable', $message);
            $this->assertStringContainsString('PHP process user:', $message);
        }

        public function testBackupFallsBackToCopyAndRemoveWhenRenameFails(): void
        {
            $handle = 'package_backup_rename_fallback_test';
            $packagePath = DIR_PACKAGES . '/' . $handle;
            $trash = app('config')->get('concrete.misc.package_backup_directory');
            $fileService = app(File::class);
            $result = null;
            $sourceExists = true;
            $backupContent = null;

            if (is_dir($packagePath)) {
                $fileService->removeAll($packagePath, true);
            }
            foreach (glob($trash . '/' . $handle . '_*') ?: [] as $backupPath) {
                if (is_dir($backupPath)) {
                    $fileService->removeAll($backupPath, true);
                }
            }
            if (!is_dir($trash) && !@mkdir($trash, app('config')->get('concrete.filesystem.permissions.directory'), true)) {
                $this->markTestSkipped('Unable to create the package trash directory.');
            }
            if (!@mkdir($packagePath, 0777, true)) {
                $this->markTestSkipped('Unable to create the test package directory.');
            }
            file_put_contents($packagePath . '/probe.txt', 'ok');

            $package = new class(app(), $handle) extends Package {
                protected $pkgHandle;

                public function __construct(Application $app, string $handle)
                {
                    parent::__construct($app);
                    $this->pkgHandle = $handle;
                }
            };

            try {
                $GLOBALS['PACKAGE_BACKUP_TEST_FORCE_RENAME_FAILURE'] = true;
                $result = $package->backup();
                $sourceExists = is_dir($packagePath);
                $backupFiles = glob($trash . '/' . $handle . '_*/probe.txt') ?: [];
                if (count($backupFiles) === 1) {
                    $backupContent = file_get_contents($backupFiles[0]);
                }
            } finally {
                unset($GLOBALS['PACKAGE_BACKUP_TEST_FORCE_RENAME_FAILURE']);
                if (is_dir($packagePath)) {
                    $fileService->removeAll($packagePath, true);
                }
                foreach (glob($trash . '/' . $handle . '_*') ?: [] as $backupPath) {
                    if (is_dir($backupPath)) {
                        $fileService->removeAll($backupPath, true);
                    }
                }
            }

            $this->assertInstanceOf(Package::class, $result);
            $this->assertFalse($sourceExists);
            $this->assertSame('ok', $backupContent);
        }

        public function testBackupPreflightCreatesNestedTrashDirectory(): void
        {
            $handle = 'package_backup_nested_trash_' . uniqid();
            $packagePath = DIR_PACKAGES . '/' . $handle;
            $testRoot = DIR_FILES_UPLOADED_STANDARD . '/' . $handle;
            $trash = $testRoot . '/first/second/trash';
            $config = app('config');
            $originalTrash = $config->get('concrete.misc.package_backup_directory');
            $fileService = app(File::class);
            $preflight = null;
            $result = null;
            $trashCreatedByPreflight = false;

            try {
                if (!@mkdir($testRoot, 0777, true) || !@mkdir($packagePath, 0777, true)) {
                    $this->markTestSkipped('Unable to create the test directories.');
                }
                file_put_contents($packagePath . '/probe.txt', 'nested');
                $config->set('concrete.misc.package_backup_directory', $trash);
                $package = $this->createPackage($handle);

                $preflight = $package->getBackupErrors();
                $trashCreatedByPreflight = is_dir($trash);
                $result = $package->backup();
            } finally {
                $config->set('concrete.misc.package_backup_directory', $originalTrash);
                if (is_dir($packagePath)) {
                    $fileService->removeAll($packagePath, true);
                }
                if (is_dir($testRoot)) {
                    $fileService->removeAll($testRoot, true);
                }
            }

            $this->assertInstanceOf(ErrorList::class, $preflight);
            $this->assertFalse($preflight->has(), (string) $preflight);
            $this->assertTrue($trashCreatedByPreflight);
            $this->assertInstanceOf(Package::class, $result);
        }

        public function testBackupRejectsTrashPathThatIsAFile(): void
        {
            $handle = 'package_backup_file_trash_' . uniqid();
            $packagePath = DIR_PACKAGES . '/' . $handle;
            $testRoot = DIR_FILES_UPLOADED_STANDARD . '/' . $handle;
            $trash = $testRoot . '/trash';
            $config = app('config');
            $originalTrash = $config->get('concrete.misc.package_backup_directory');
            $fileService = app(File::class);
            $result = null;
            $sourceStillExists = false;
            $trashContent = null;

            try {
                if (!@mkdir($testRoot, 0777, true) || !@mkdir($packagePath, 0777, true)) {
                    $this->markTestSkipped('Unable to create the test directories.');
                }
                file_put_contents($trash, 'sentinel');
                file_put_contents($packagePath . '/probe.txt', 'source');
                $config->set('concrete.misc.package_backup_directory', $trash);

                $result = $this->createPackage($handle)->backup();
                $sourceStillExists = is_file($packagePath . '/probe.txt');
                $trashContent = file_get_contents($trash);
            } finally {
                $config->set('concrete.misc.package_backup_directory', $originalTrash);
                if (is_dir($packagePath)) {
                    $fileService->removeAll($packagePath, true);
                }
                if (is_dir($testRoot)) {
                    $fileService->removeAll($testRoot, true);
                }
            }

            $this->assertInstanceOf(ErrorList::class, $result);
            $this->assertStringContainsString('exists but is not a directory', (string) $result);
            $this->assertTrue($sourceStillExists);
            $this->assertSame('sentinel', $trashContent);
        }

        private function createPackage(string $handle): Package
        {
            return new class(app(), $handle) extends Package {
                protected $pkgHandle;

                public function __construct(Application $app, string $handle)
                {
                    parent::__construct($app);
                    $this->pkgHandle = $handle;
                }
            };
        }
    }
}
