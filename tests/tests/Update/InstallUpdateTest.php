<?php

namespace Concrete\Tests\Update;

use Concrete\TestHelpers\DockerTrait;
use PHPUnit_Framework_TestCase;

/**
 * @group docker
 */
class InstallUpdateTest extends PHPUnit_Framework_TestCase
{
    use DockerTrait;

    public function testFullInstallation()
    {
        $this->runScriptInDocker('ghcr.io/concrete5-community/docker5:8.5.2-full', 'run-install.sh');
    }

    public function testUpdatePreviousInstallation()
    {
        $this->runScriptInDocker('ghcr.io/concrete5-community/docker5:5.7.5.13-full', 'run-update.sh');
    }
}
