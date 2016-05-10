<?php
namespace Concrete\Core\Config;

interface SaverInterface
{
    /**
     * Save config item.
     *
     * @param string $item
     * @param string $value
     * @param string $environment
     * @param string $group
     * @param string|null $namespace
     *
     * @return bool
     */
    public function save($item, $value, $environment, $group, $namespace = null);
}
