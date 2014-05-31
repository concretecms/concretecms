<?
namespace Concrete\Core\File\StorageLocation\Configuration;

abstract class Configuration
{

    abstract public function loadFromRequest(\Concrete\Core\Http\Request $req);
		

}