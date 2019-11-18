<?php
namespace Concrete\Core\Board\DataSource\Driver;

use Concrete\Core\Application\UserInterface\Icon\BasicIconFormatter;
use Concrete\Core\Application\UserInterface\Icon\IconFormatterInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class PageDriver extends AbstractDriver
{
    
    public function getIconFormatter(): IconFormatterInterface
    {
        return new BasicIconFormatter('fas fa-file');
    }

}
