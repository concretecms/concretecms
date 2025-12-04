<?php

namespace Concrete\Core\Mail\Transport;

use Symfony\Component\Mailer\Transport\TransportInterface;

interface FactoryInterface
{
    /**
     * Create a Transport instance from a configuration array.
     *
     * @param array $array
     *
     * @return TransportInterface
     */
    public function createTransportFromArray(array $array);
}