<?php
namespace Concrete\Core\Package;

use AuthenticationType;
use Concrete\Core\Application\Application;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\File\Filesystem;
use Config;
use Core;
use Database;
use Group;
use GroupTree;
use Page;
use PermissionKey;
use UserInfo;

/**
 * @deprecated
 */
class StartingPointPackage extends Package
{

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->routines = [
            new StartingPointInstallRoutine('install_content', 86, t('Adding pages and content.')),
        ];
    }

    protected function install_content()
    {
        $ci = new ContentImporter();
        $ci->importContentFile($this->getPackagePath() . '/content.xml');
    }




}
