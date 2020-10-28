<?php
namespace Concrete\Core\Messenger\Registry;

use Symfony\Component\Messenger\MessageBusInterface;

interface RegistryInterface
{

    public function getBusBuilder(string $handle): callable;

}