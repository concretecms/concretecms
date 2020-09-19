<?php
namespace Concrete\Core\Automation\Task\Controller;

use Concrete\Core\Automation\Task\Input\InputInterface;
use Concrete\Core\Foundation\Command\CommandInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class RescanFilesController extends AbstractController
{

    public function getName(): string
    {
        return t('Rescan Files');
    }

    public function getDescription(): string
    {
        return t('Recomputes all attributes, clears and regenerates all thumbnails for a file.');
    }

    public function getCommand(InputInterface $input): CommandInterface
    {
        // TODO: Implement getCommand() method.
    }
}
