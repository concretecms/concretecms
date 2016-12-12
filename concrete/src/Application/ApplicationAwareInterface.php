<?php
namespace Concrete\Core\Application;

/**
 * Interface ApplicationAwareInterface
 * This interface declares awareness of the concrete5 application.
 *
 * \@package Concrete\Core\Application
 */
interface ApplicationAwareInterface
{
    /**
     * Set the application object.
     *
     * @param \Concrete\Core\Application\Application $application
     */
    public function setApplication(Application $application);
}
