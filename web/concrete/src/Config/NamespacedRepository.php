<?php

namespace Concrete\Core\Config;
use Concrete\Core\Config\Repository;
use Package;

class NamespacedRepository extends Repository
{

	/**
	 * @var string
	 */
	protected $namespace;

	public function getNamespace() { return $this->namespace; }
	public function setNamespace($namespace) { $this->namespace = $namespace; }


    /**
     * Save a key (adding the internal namespace)
     *
     * @param $key
     * @param $value
     * @return bool
     */
    public function save($key, $value)
	{
		list($namespace, $group, $item) = $this->parseKey($key);
		$nKey = $key;
		if ( $namespace != $this->namespace ) $nKey = $this->namespace . "::$key";
		parent::save($nKey, $value);
	}

	/**
	 * Get the specified configuration value adding the namespace.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		list($namespace, $group, $item) = $this->parseKey($key);
		$nKey = $key;
		if ( $namespace != $this->namespace ) $nKey = $this->namespace . "::$key";
		return parent::get($nKey, $default);
	}

    /**
     * Create a new configuration repository.
     *
	 * @param string		  $namespace
     * @param LoaderInterface $loader
     * @param SaverInterface  $saver
     * @param                 $environment
     */
    public function __construct($namespace, LoaderInterface $loader, SaverInterface $saver, $environment)
	{
		$this->namespace = $namespace;
		parent::__construct($loader, $saver, $environment);
	}

	public static function getDatabaseRepository( $namespace )
	{
		$loader = new DatabaseLoader();
		$saver = new DatabaseSaver();
		$cms = \Core::make( 'app' );
		return new NamespacedRepository( $namespace, $loader, $saver, $cms->environment() );
	}

	public static function getFileRepository( $namespace )
	{
		$file_system = new Filesystem();
		$loader = new FileLoader($file_system);
		$saver = new FileSaver($file_system);
		$cms = \Core::make( 'app' );
		return new NamespacedRepository( $namespace, $loader, $saver, $cms->environment() );
	}

	public static function getRepositoryForPackage( $packageOrpackageIDorHandle )
	{
		$handle = $packageOrpackageIDorHandle;
		if ( is_object( $packageOrpackageIDorHandle ) ) $handle = $packageOrpackageIDorHandle->getPackageHandle();
		else if ( is_numeric( $packageOrpackageIDorHandle ) )
		{
			$package = Package::getByID( $packageOrpackageIDorHandle );
			if ( !$package ) return null;
			$handle = $package->getPackageHandle();
		}
		return self::getDatabaseRepository($handle);
	
	}
}


// vim: set noexpandtab ts=4 :
