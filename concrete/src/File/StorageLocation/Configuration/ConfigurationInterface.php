<?php
namespace Concrete\Core\File\StorageLocation\Configuration;

use Concrete\Core\Error\ErrorList\Error\Error;

interface ConfigurationInterface
{
    /**
     * Does this storage location have a public url
     * @return bool
     */
    public function hasPublicURL();

    /**
     * Does this storage location have a relative path
     * @return bool
     */
    public function hasRelativePath();

    /**
     * Load in data from a request object
     * @param \Concrete\Core\Http\Request $req
     * @return void
     */
    public function loadFromRequest(\Concrete\Core\Http\Request $req);

    /**
     * Validate a request, this is used during saving
     * @param \Concrete\Core\Http\Request $req
     * @return Error
     */
    public function validateRequest(\Concrete\Core\Http\Request $req);

    /**
     * Get the flysystem adapter
     * @return \League\Flysystem\AdapterInterface
     */
    public function getAdapter();

    /**
     * Get the public url to a file
     * @param string $file
     * @return string
     */
    public function getPublicURLToFile($file);

    /**
     * Get the relative path to a file
     * @param string $file
     * @return string
     */
    public function getRelativePathToFile($file);
}
