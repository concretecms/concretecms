<?php
namespace Concrete\Core\Service\Configuration\HTTP;

use Concrete\Core\Service\Configuration\StorageInterface;
use Exception;

class NginxStorage implements StorageInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\StorageInterface::canRead()
     */
    public function canRead()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\StorageInterface::read()
     */
    public function read()
    {
        throw new Exception(t('Reading nginx configuration is not implemented'));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\StorageInterface::canWrite()
     */
    public function canWrite()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\StorageInterface::write()
     */
    public function write($configuration)
    {
        throw new Exception(t('Writing nginx configuration is not implemented'));
    }
}
