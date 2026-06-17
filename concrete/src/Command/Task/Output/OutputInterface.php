<?php
namespace Concrete\Core\Command\Task\Output;

defined('C5_EXECUTE') or die("Access Denied.");

interface OutputInterface
{

    public function write($message): void;

    public function writeError($message): void;


}
