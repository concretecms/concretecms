<?php
namespace Concrete\Core\Foundation\Command;

interface AsynchronousBusInterface extends BusInterface
{

    public function getQueue();

}
