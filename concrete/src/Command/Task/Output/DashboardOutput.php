<?php
namespace Concrete\Core\Command\Task\Output;

use Concrete\Core\Command\Task\TaskInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * An output class used when tasks are started in the Dashboard.
 */
class DashboardOutput implements OutputInterface
{

    public function write($message)
    {

    }



}
