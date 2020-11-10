<?php

namespace Concrete\Core\File\ExternalFileProvider;

use Concrete\Core\File\ExternalFileProvider\Configuration\ConfigurationInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="FileExternalFileProviders")
 */
interface ExternalFileProviderInterface
{

    /**
     * Get the configuration for this external file provider
     * @return ConfigurationInterface
     */
    public function getConfigurationObject();

    /**
     * Delete this external file provider
     *
     * @return mixed
     */
    public function delete();

    /**
     * Save a external file provider
     *
     * @return mixed
     */
    public function save();

}
