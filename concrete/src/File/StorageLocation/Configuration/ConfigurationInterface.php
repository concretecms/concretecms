<?php
namespace Concrete\Core\File\StorageLocation\Configuration;

interface ConfigurationInterface
{

    public function hasPublicURL();
    public function hasRelativePath();
    public function loadFromRequest(\Concrete\Core\Http\Request $req);
    public function validateRequest(\Concrete\Core\Http\Request $req);
	public function getAdapter();
    public function getPublicURLToFile($file);
    public function getRelativePathToFile($file);

}
