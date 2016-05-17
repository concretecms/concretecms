<?php
namespace Concrete\Core\Service\Configuration\HTTP;

use Concrete\Core\Service\Configuration\ConfiguratorInterface;
use Exception;
use Concrete\Core\Service\Rule\RuleInterface;

class NginxConfigurator implements ConfiguratorInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\ConfiguratorInterface::hasRule()
     */
    public function hasRule($configuration, RuleInterface $rule)
    {
        throw new Exception(t('Managing nginx configuration is not implemented'));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\ConfiguratorInterface::addRule()
     */
    public function addRule($configuration, RuleInterface $rule)
    {
        throw new Exception(t('Managing nginx configuration is not implemented'));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\ConfiguratorInterface::removeRule()
     */
    public function removeRule($configuration, RuleInterface $rule)
    {
        throw new Exception(t('Managing nginx configuration is not implemented'));
    }
}
