<?php
namespace Concrete\Core\Application;

/**
 * This interface declares awareness of the concrete5 application.
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
