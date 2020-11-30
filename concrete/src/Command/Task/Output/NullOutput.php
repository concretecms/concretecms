<?php
namespace Concrete\Core\Command\Task\Output;

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * An output class that discards output sent to it..
 */
class NullOutput implements OutputInterface
{

    public function write($message)
    {
        return null;
    }



}
