<?php
namespace Concrete\Core\Install\StartingPoint\Installer;

use Concrete\Core\Feature\Features;
use Concrete\Core\Install\InstallerOptions;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Backend\CreateBackendNavigationRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Backend\ReorderBackendRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Backend\SetupBackendPermissionsRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\AddExpressObjectsSupportRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\AddHomePageRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\AddTreeNodesRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\AddUsersRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\CreateDirectoriesRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\FinishInstallationRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\InstallApiRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\InstallDatabaseRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\InstallFileManagerSupportRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\InstallSiteRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\SetDefaultConversationPermissionsRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\SetDefaultConversationSubscribersRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Base\SetupSitePermissionsRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Frontend\ImportStartingPointContentRoutine;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Frontend\ImportStartingPointFilesRoutine;
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
            new InstallFeatureContentRoutine('base', Features::PERMISSIONS, t('Adding core permissions support.')),
            new InstallFeatureContentRoutine('base', 'permissions/boards', t('Adding Boards permission support.')),
            new InstallFeatureContentRoutine('base', 'permissions/calendar', t('Adding Calendar permissions support.')),
            new InstallFeatureContentRoutine('base', 'permissions/conversations', t('Adding Conversations permissions support.')),
            new SetDefaultConversationSubscribersRoutine(),
            new SetDefaultConversationPermissionsRoutine(),
            new InstallFeatureContentRoutine('base', 'permissions/express', t('Adding Express permissions support.')),
            new InstallFeatureContentRoutine('base', 'permissions/multilingual', t('Adding multilingual permission support.')),
            new AddTreeNodesRoutine(),
            new InstallFeatureContentRoutine('base', Features::ATTRIBUTES, t('Adding core attribute types and categories.')),
            new InstallFeatureContentRoutine('base', 'attributes/calendar', t('Adding calendar attributes.')),
            new InstallFeatureContentRoutine('base', 'attributes/express', t('Adding Express attributes.')),
            new AddHomePageRoutine(),
            new AddExpressObjectsSupportRoutine(),
            new InstallFeatureContentRoutine('base', Features::PAGES, t('Adding base required single pages.')),
            new InstallFeatureContentRoutine('base', Features::BOARDS, t('Adding board data sources.')),
            new InstallFeatureContentRoutine('base', Features::CONVERSATIONS, t('Adding conversation components.')),
            new InstallFeatureContentRoutine('base', Features::AUTOMATION, t('Adding tasks.')),
            new InstallApiRoutine(),
            new InstallFileManagerSupportRoutine(),
        ];
    }

    protected function getContentRoutines(string $domain, \Closure $displayMethod) {

        $features = Features::getFeatures();
        $routines = [];
        foreach ($features as $identifier) {
            $routine = new InstallFeatureContentRoutine(
                $domain,
                $identifier,
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

        $routines = [];
        $routines[] = new InstallFeatureContentRoutine(
            'cms',
            null,
            t('Installing CMS.')
        );
        foreach ($this->getContentRoutines('cms', function($identifier) {
            return t('Installing CMS: %s', Features::getDisplayName($identifier));
        }) as $routine) {
            $routines[] = $routine;
        }

        return $routines;
    }

    protected function getBackendRoutines(): array
    {
        $routines = [];
        $routines[] = new InstallFeatureContentRoutine(
            'backend',
            null,
            t('Installing backend.')
        );

        foreach ($this->getContentRoutines('backend', function($identifier) {
            return t('Installing Admin Backend: %s', Features::getDisplayName($identifier));
        }) as $routine) {
            $routines[] = $routine;
        }
        $routines[] = new SetupBackendPermissionsRoutine();
        $routines[] = new CreateBackendNavigationRoutine();
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
        $routines[] = new InstallFeatureContentRoutine(
            'frontend',
            null,
            t('Installing Frontend.')
        );
        foreach ($this->getContentRoutines('frontend', function($identifier) {
            return t('Installing Frontend Content: %s', Features::getDisplayName($identifier));
        }) as $routine) {
            $routines[] = $routine;
        }
        $routines[] = new InstallFeatureContentRoutine('base', Features::THEMES, t('Adding themes.'));
        $routines[] = new ImportStartingPointFilesRoutine();
        $routines[] = new ImportStartingPointContentRoutine();
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
