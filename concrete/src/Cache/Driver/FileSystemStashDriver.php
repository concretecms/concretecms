<?php

namespace Concrete\Core\Cache\Driver;

use Stash\Driver\FileSystem;
use Stash\Exception\WindowsPathMaxLengthException;

class FileSystemStashDriver extends FileSystem
{

    /**
     * This function takes the data and stores it to the path specified. If the directory leading up to the path does
     * not exist, it creates it.
     *
     * We overrode this function because calls to opcache_invalidate will sometimes throw fatals
     * See https://github.com/tedious/Stash/issues/350
     *
     * {@inheritdoc}
     * @throws \Stash\Exception\WindowsPathMaxLengthException
     */
    public function storeData($key, $data, $expiration)
    {
        $path = $this->makePath($key);

        // MAX_PATH is 260 - http://msdn.microsoft.com/en-us/library/aa365247(VS.85).aspx
        if (strlen($path) > 259 &&  stripos(PHP_OS, 'WIN') === 0) {
            throw new WindowsPathMaxLengthException();
        }

        if (!file_exists($path)) {
            if (!is_dir(dirname($path))) {
                if (!@mkdir(dirname($path), $this->dirPermissions, true)) {
                    return false;
                }
            }

            if (!(touch($path) && chmod($path, $this->filePermissions))) {
                return false;
            }
        }

        $storeString = $this->getEncoder()->serialize($this->makeKeyString($key), $data, $expiration);
        $result = file_put_contents($path, $storeString, LOCK_EX);

        // If opcache is switched on, it will try to cache the PHP data file
        // The new php opcode caching system only revalidates against the source files once every few seconds,
        // so some changes will not be caught.
        // This fix immediately invalidates that opcode cache after a file is written,
        // so that future includes are not using the stale opcode cached file.
        if (function_exists('opcache_invalidate')) {
            $invalidate = true;

            if ($restrictedDirectory = ini_get('opcache.restrict_api')) {
                if (strpos(__FILE__, $restrictedDirectory) !== 0) {
                    $invalidate = false;
                }
            }

            if ($invalidate) {
                @opcache_invalidate($path, true);
            }
        }

        return false !== $result;
    }

}
