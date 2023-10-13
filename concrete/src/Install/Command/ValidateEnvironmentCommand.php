<?php

namespace Concrete\Core\Install\Command;

use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\Install\InstallEnvironment;

class ValidateEnvironmentCommand extends Command
{

    /**
     * @var InstallEnvironment
     */
    protected $environment;

    public function __construct(InstallEnvironment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return InstallEnvironment
     */
    public function getEnvironment(): InstallEnvironment
    {
        return $this->environment;
    }



}
