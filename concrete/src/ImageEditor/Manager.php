<?php
namespace Concrete\Core\ImageEditor;

use Concrete\Core\Application\Application;
use Concrete\Core\ImageEditor\Controller\DefaultEditorController;
use Concrete\Core\Support\Manager as CoreManager;

defined('C5_EXECUTE') or die("Access Denied.");

class Manager extends CoreManager
{
    public function createDefaultDriver()
    {
        return new DefaultEditorController();
    }

    public function __construct(Application $application)
    {
        parent::__construct($application);
    }
}
