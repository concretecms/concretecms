<?php

namespace Concrete\Tests\Api\OAuth;

use Concrete\Core\Api\OAuth\Scope\ScopeRegistry;
use Concrete\Core\Api\OAuth\Scope\ScopeRegistryInterface;
use Concrete\Core\Entity\OAuth\Scope;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Tests\TestCase;

class ScopeRegistryTest extends TestCase
{
    public function testRegistry()
    {
        $app = Facade::getFacadeApplication();
        $registry = $app->make(ScopeRegistryInterface::class);
        $this->assertInstanceOf(ScopeRegistry::class, $registry);
        $scopes = $registry->getScopes();
        $this->assertCount(5, $scopes);
        $this->assertInstanceOf(Scope::class, $scopes[0]);
        $this->assertEquals('openid', $scopes[0]->getIdentifier());
        $this->assertEquals('Remotely authenticate into Concrete.', $scopes[0]->getDescription());
    }
    

}
