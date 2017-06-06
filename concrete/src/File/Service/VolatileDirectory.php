<?php
namespace Concrete\Core\File\Service;

use Exception;
use Illuminate\Filesystem\Filesystem;

class VolatileDirectory
{
    /**
     * The used Filesystem instance.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * The path of this volatile directory.
     *
     * @var string
     */
    protected $path;

    /**
     * Initializes the instance.
     *
     * @param Filesystem $filesystem the Filesystem instance to use
     * @param string $parentDirectory the parent directory that will contain this volatile directory
     *
     * @throws Exception
     */
    public function __construct(Filesystem $filesystem, $parentDirectory)
    {
        $this->filesystem = $filesystem;
        $parentDirectory = is_string($parentDirectory) ? rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $parentDirectory), '/') : '';
        if ($parentDirectory === '') {
            throw new Exception(t('Unable to retrieve the temporary directory.'));
        }
        if (!$this->filesystem->isWritable($parentDirectory)) {
            throw new Exception(t('The temporary directory is not writable.'));
        }
        for ($i = 0; ; ++$i) {
            $path = $parentDirectory . '/volatile-' . $i . '-' . uniqid();
            if (!$this->filesystem->exists($path)) {
                if (@$this->filesystem->makeDirectory($path, DIRECTORY_PERMISSIONS_MODE_COMPUTED)) {
                    break;
                }
            }
        }
        $this->path = $path;
    }

    /**
     * Get the used Filesystem instance.
     *
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * Get the absolute path of this volatile directory (always with '/' as directory separator, without the trailing '/').
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Clear and delete this volatile directory.
     */
    public function __destruct()
    {
        if ($this->path !== null) {
            try {
                $this->filesystem->deleteDirectory($this->path);
            } catch (Exception $foo) {
            }
            $this->path = null;
        }
    }
}
