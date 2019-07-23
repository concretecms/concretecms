<?php

namespace Concrete\Core\System\Mutex;

/**
 * Trait that contains stuff that can be useful for Mutexes.
 */
trait MutexTrait
{
    /**
     * The temporary directory.
     *
     * @var string
     */
    protected $temporaryDirectory;

    /**
     * Set the temporary directory.
     *
     * @param string $value
     * @param mixed $temporaryDirectory
     */
    protected function setTemporaryDirectory($temporaryDirectory)
    {
        $this->temporaryDirectory = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $temporaryDirectory), '/');
    }

    /**
     * Get the full path of a temporary file that's unique for the concrete5 application and for the specified mutex key.
     *
     * @param string $key The mutex key
     * @param mixed $mutexKey
     *
     * @throws InvalidMutexKeyException
     *
     * @return string
     */
    protected function getFilenameForMutexKey($mutexKey)
    {
        $mutexKeyString = (is_string($mutexKey) || is_int($mutexKey)) ? (string) $mutexKey : '';
        if ($mutexKeyString === '') {
            throw new InvalidMutexKeyException($mutexKey);
        }
        if (preg_match('/^[a-zA-Z0-9_\-]{1,50}$/', $mutexKeyString)) {
            $filenameChunk = $mutexKeyString;
        } else {
            $filenameChunk = sha1($mutexKeyString);
        }

        return $this->temporaryDirectory . '/mutex-' . md5(DIR_APPLICATION) . '-' . $filenameChunk . '.lock';
    }
}
