<?php
namespace Concrete\Core\Service\Manager;

use Concrete\Core\Service\ServiceInterface;

interface ManagerInterface
{
    /**
     * Add an extension to this manager.
     *
     * @param string $handle
     * @param string|callable|ServiceInterface $abstract
     */
    public function extend($handle, $abstract);

    /**
     * An array of handles that have been added with `->extend`.
     *
     * @return string[]
     */
    public function getExtensions();

    /**
     * Does this handle exist?
     * This method MUST return true for anything added with `->extend`.
     *
     * @param $handle
     *
     * @return bool
     */
    public function has($handle);

    /**
     * Get the driver for this handle.
     *
     * @param $handle
     *
     * @return ServiceInterface|null
     */
    public function getService($handle);

    /**
     * Loops through the bound services and returns the ones that are active.
     *
     * @return ServiceInterface[]
     */
    public function getActiveServices();
}
