<?php
namespace Concrete\Core\Service\Configuration\HTTP;

use Concrete\Core\Service\Configuration\StorageInterface;
use Exception;
use Illuminate\Filesystem\Filesystem;

class ApacheStorage implements StorageInterface
{
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
        $fs = new Filesystem();
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
        $fs = new Filesystem();
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
        $fs = new Filesystem();

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
        $fs = new Filesystem();
        if (@$fs->put($ht, $configuration) === false) {
            throw new Exception("Failed to write to file $ht");
        }
    }
}
