<?php
namespace Concrete\Core\Logging\Configuration;

use Concrete\Core\Entity\Site\Site;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SimpleFileConfiguration extends SimpleConfiguration
{

    /**
     * @var string
     */
    protected $filename;

    public function __construct($filename, $coreLevel = Logger::DEBUG)
    {
        $this->filename = $filename;
        parent::__construct($coreLevel);
    }

    public function createHandler($level)
    {
        $handler = new StreamHandler($this->filename, $this->coreLevel);
        return $handler;
    }



}



