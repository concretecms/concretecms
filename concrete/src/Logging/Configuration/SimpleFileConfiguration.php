<?php
namespace Concrete\Core\Logging\Configuration;

use Concrete\Core\Entity\Site\Site;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SimpleFileConfiguration extends SimpleConfiguration
{

    /**
     * @var Site
     */
    protected $site;

    /**
     * @var string
     */
    protected $directory;

    public function __construct(Site $site, $directory, $coreLevel = Logger::DEBUG)
    {
        $this->site = $site;
        $this->directory = rtrim($directory, '/' . DIRECTORY_SEPARATOR);
        parent::__construct($coreLevel);
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function getFileName()
    {
        $name = $this->site->getSiteName();
        if (!$name) {
            $name = 'concrete5';
        }
        return str_replace('_', '-', trim(snake_case($name))) . '.log';
    }

    public function createHandler($level)
    {
        $path = $this->getDirectory() . '/' . $this->getFileName();
        $handler = new StreamHandler($path, $this->coreLevel);
        return $handler;
    }



}



