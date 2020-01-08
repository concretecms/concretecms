<?php

namespace Concrete\Core\Board\DataSource;

use Concrete\Core\Controller\ElementController;

defined('C5_EXECUTE') or die("Access Denied.");

abstract class DataSourceElementController extends ElementController
{
    
    protected $configuredDataSource;

    /**
     * @param mixed $configuredDataSource
     */
    public function setConfiguredDataSource($configuredDataSource): void
    {
        $this->configuredDataSource = $configuredDataSource;
    }
    
    
    
}
