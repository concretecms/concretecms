<?php
namespace Concrete\Core\Messenger\Registry;

use Symfony\Component\Messenger\MessageBusInterface;

interface RegistryInterface
{

    /**
     * @param string $handle
     * @return callable
     */
    public function getBusBuilder(string $handle): callable;

    public function getReceivers(): iterable;

}