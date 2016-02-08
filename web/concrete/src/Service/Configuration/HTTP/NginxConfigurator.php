<?php
namespace Concrete\Core\Service\Configuration\HTTP;

use Concrete\Core\Service\Configuration\ConfiguratorInterface;
use Exception;

class NginxConfigurator implements ConfiguratorInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\ConfiguratorInterface::hasRule()
     */
    public function hasRule($configuration, array $rule)
    {
        throw new Exception(t('Managing nginx configuration is not implemented'));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\ConfiguratorInterface::addRule()
     */
    public function addRule($configuration, array $rule)
    {
        throw new Exception(t('Managing nginx configuration is not implemented'));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\ConfiguratorInterface::removeRule()
     */
    public function removeRule($configuration, array $rule)
    {
        throw new Exception(t('Managing nginx configuration is not implemented'));
    }
}
