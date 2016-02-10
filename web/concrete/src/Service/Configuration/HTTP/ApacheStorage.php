<?php
namespace Concrete\Core\Service\Configuration\HTTP;

use Concrete\Core\Service\Configuration\StorageInterface;
use Exception;
use Illuminate\Filesystem\Filesystem;

class ApacheStorage implements StorageInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem = null;

    /**
     * Set the Filesystem to use.
     *
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Get the Filesystem to use.
     *
     * @return Filesystem
     */
    public function getFilesystem()
    {
        if ($this->filesystem === null) {
            $this->filesystem = new Filesystem();
        }

        return $this->filesystem;
    }

    /**
     * Return the full path name to the .htaccess file.
     *
     * @return string
     */
    protected function getHTaccessFilename()
    {
        return DIR_BASE.'/.htaccess';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\StorageInterface::canRead()
     */
    public function canRead()
    {
        $ht = $this->getHTaccessFilename();
        $fs = $this->getFilesystem();
        if (@$fs->isFile($ht)) {
            $result = @is_readable($ht);
        } else {
            $result = @is_readable(@dirname($ht));
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\StorageInterface::read()
     */
    public function read()
    {
        $result = '';
        $ht = $this->getHTaccessFilename();
        $fs = $this->getFilesystem();
        if (@$fs->isFile($ht)) {
            $result = @$fs->get($ht);
            if ($result === false) {
                throw new Exception("Failed to read from file $ht");
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\StorageInterface::canWrite()
     */
    public function canWrite()
    {
        $ht = $this->getHTaccessFilename();
        $fs = $this->getFilesystem();

        return (bool) (@$fs->isFile($ht) ? @$fs->isWritable($ht) : @$fs->isWritable(@dirname($ht)));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\StorageInterface::write()
     */
    public function write($configuration)
    {
        $ht = $this->getHTaccessFilename();
        $fs = $this->getFilesystem();
        if (@$fs->put($ht, $configuration) === false) {
            throw new Exception("Failed to write to file $ht");
        }
    }
}
