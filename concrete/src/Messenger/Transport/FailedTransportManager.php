<?php
namespace Concrete\Core\Messenger\Transport;

use Psr\Container\ContainerInterface;

class FailedTransportManager extends TransportManager
{

    protected $defaultFailedReceiverName;

    protected $failureRoutes = [];

    /**
     * @return mixed
     */
    public function getDefaultFailedReceiverName()
    {
        return $this->defaultFailedReceiverName;
    }

    /**
     * @param mixed $defaultFailedReceiverName
     */
    public function setDefaultFailedReceiverName($defaultFailedReceiverName): void
    {
        $this->defaultFailedReceiverName = $defaultFailedReceiverName;
    }

    public function routeFailedReceiverToSender(string $receiver, string $sender)
    {
        $this->failureRoutes[$receiver] = $sender;
    }

    public function getFailureSenders()
    {
        return new class($this->failureRoutes, $this->senders) implements ContainerInterface {

            protected $routes;
            protected $senders;

            public function __construct($routes, $senders)
            {
                $this->routes = $routes;
                $this->senders = $senders;
            }

            public function has(string $id)
            {
                return array_key_exists($id, $this->routes);
            }

            public function get(string $id)
            {
                $sender = $this->routes[$id];
                return $this->senders->get($sender);
            }
        };

    }



}