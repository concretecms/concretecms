<?php
namespace Concrete\Core\Service\Configuration\HTTP;

use Concrete\Core\Service\Configuration\StorageInterface;
use Exception;

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
        if (is_file($ht)) {
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
        if (is_file($ht)) {
            $result = @file_get_contents($ht);
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

        return (bool) (@is_file($ht) ? @is_writable($ht) : @is_writable(@dirname($ht)));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\StorageInterface::write()
     */
    public function write($configuration)
    {
        $ht = $this->getHTaccessFilename();
        if (@file_put_contents($ht, $configuration) === false) {
            throw new Exception("Failed to write to file $ht");
        }
    }
}
