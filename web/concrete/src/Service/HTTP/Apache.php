<?php
namespace Concrete\Core\Service\HTTP;

use Concrete\Core\Application\Application;
use Concrete\Core\Service\Detector\DetectorInterface;
use Concrete\Core\Service\ServiceInterface;

class Apache implements ServiceInterface
{
    /**
     * The class to use for our detector.
     *
     * @var string
     */
    protected $detector_class = 'Concrete\Core\Service\Detector\HTTP\ApacheDetector';

    /**
     * The class to use for our generator.
     *
     * @var string
     */
    protected $generator_class = 'Concrete\Core\Service\Configuration\HTTP\ApacheGenerator';

    /**
     * The class to use to read/write options.
     *
     * @var string
     */
    protected $storage_class = 'Concrete\Core\Service\Configuration\HTTP\ApacheStorage';

    /**
     * The class to use to manage configuration rules.
     *
     * @var string
     */
    protected $configurator_class = 'Concrete\Core\Service\Configuration\HTTP\ApacheConfigurator';

    /**
     * @var DetectorInterface
     */
    protected $detector;

    /**
     * @var Application
     */
    protected $app;

    /**
     * Service version.
     *
     * @var string
     */
    protected $version;

    /**
     * Class constructor.
     *
     * @param Application $app
     */
    public function __construct($version, Application $app)
    {
        $this->app = $app;
        $this->version = (string) $version;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\ServiceInterface::getName()
     */
    public function getName()
    {
        return 'Apache';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\ServiceInterface::getVersion()
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\ServiceInterface::getFullName()
     */
    public function getFullName()
    {
        $name = $this->getName();
        $version = $this->getVersion();

        return ($version === '') ? $name : "$name $version";
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\ServiceInterface::getDetector()
     */
    public function getDetector()
    {
        return $this->app->make($this->detector_class);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\ServiceInterface::getGenerator()
     */
    public function getGenerator()
    {
        return $this->app->make($this->generator_class);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\ServiceInterface::getStorage()
     */
    public function getStorage()
    {
        return $this->app->make($this->storage_class);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\ServiceInterface::getConfigurator()
     */
    public function getConfigurator()
    {
        return $this->app->make($this->configurator_class);
    }
}
