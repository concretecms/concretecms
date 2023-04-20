<?php
namespace Concrete\Core\Install\StartingPoint\Installer;

use Concrete\Core\Feature\Features;
use Concrete\Core\Install\InstallerOptions;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Frontend\ImportStartingPointFilesRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Backend\ReorderBackendRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Backend\SetupBackendPermissionsRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\AddHomePageRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\AddTreeNodesRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\AddUsersRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\CreateDirectoriesRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\FinishInstallationRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\InstallApiRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\InstallDatabaseRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\InstallFileManagerSupportRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\InstallSiteRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\SetupSitePermissionsRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\InstallFeatureContentRoutine;

class Installer implements InstallerInterface
{

    protected function getBaseRoutines(): array
    {
        return [
            new CreateDirectoriesRoutine(),
            new InstallDatabaseRoutine(),
            new InstallSiteRoutine(),
            new AddUsersRoutine(),
            new InstallFeatureContentRoutine(Features::PERMISSIONS, 'base', t('Adding core permission support.')),
            new AddTreeNodesRoutine(),
            new InstallFeatureContentRoutine(Features::ATTRIBUTES, 'base', t('Adding core attribute types and categories.')),
            new AddHomePageRoutine(),
            new InstallFeatureContentRoutine(Features::PAGES, 'base', t('Adding base required single pages.')),
            new InstallFeatureContentRoutine(Features::BOARDS, 'base', t('Adding board data sources.')),
            new InstallFeatureContentRoutine(Features::AUTOMATION, 'base', t('Adding tasks.')),
            new InstallApiRoutine(),
            new InstallFileManagerSupportRoutine(),
        ];
    }

    protected function getContentRoutines(string $domain, \Closure $displayMethod) {

        $features = Features::getFeatures();
        $routines = [];
        foreach ($features as $identifier) {
            $routine = new InstallFeatureContentRoutine(
                $identifier,
                $domain,
                $displayMethod($identifier)
            );
            if ($routine->hasContentFile()) {
                $routines[] = $routine;
            }
        }
        return $routines;
    }

    protected function getCmsRoutines(): array
    {
        return $this->getContentRoutines('cms', function($identifier) {
            return t('Installing CMS: %s', Features::getDisplayName($identifier));
        });
    }

    protected function getBackendRoutines(): array
    {
        $routines = [];
        foreach ($this->getContentRoutines('backend', function($identifier) {
            return t('Installing Admin Backend: %s', Features::getDisplayName($identifier));
        }) as $routine) {
            $routines[] = $routine;
        }
        $routines[] = new SetupBackendPermissionsRoutine();
        $routines[] = new ReorderBackendRoutine();
        return $routines;
    }


    protected function getFinishRoutines(): array
    {
        return [
            new SetupSitePermissionsRoutine(),
            new FinishInstallationRoutine(),
        ];
    }

    public function getFrontendRoutines(): array
    {
        $routines = [];
        $routines[] = new InstallFeatureContentRoutine(Features::THEMES, 'base', t('Adding themes.'));
        $routines[] = new ImportStartingPointFilesRoutine();
        return $routines;
    }

    public function getInstallCommands(InstallerOptions $options): array
    {
        $baseRoutines = $this->getBaseRoutines();
        $cmsRoutines = $this->getCmsRoutines();
        $backendRoutines = $this->getBackendRoutines();
        $frontendRoutines = $this->getFrontendRoutines();
        $finishRoutines = $this->getFinishRoutines();
        return array_merge($baseRoutines, $cmsRoutines, $backendRoutines, $frontendRoutines, $finishRoutines);
    }
}
