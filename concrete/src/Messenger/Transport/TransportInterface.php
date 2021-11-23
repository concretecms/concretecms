<?php
namespace Concrete\Core\Messenger\Transport;

use Symfony\Component\Messenger\Transport\TransportInterface as WrappedTransport;
/**
 * A convenient wrapper for all of the various objects that comprise a Symfony Messenger Transport.
 */
interface TransportInterface
{

    const DEFAULT_ASYNC = 'async';

    /**
     * @return callable[]
     */
    public function getSenders(): iterable;

    /**
     * @return callable[]
     */
    public function getReceivers(): iterable;

}