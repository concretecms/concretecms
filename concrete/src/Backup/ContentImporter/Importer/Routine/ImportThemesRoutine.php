<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Utility\Service\Xml;
use Doctrine\ORM\EntityManager;

class ImportThemesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'themes';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->themes)) {
            $xml = app(Xml::class);
            foreach ($sx->themes->theme as $th) {
                $pkg = static::getPackageObject($th['package']);
                $pThemeHandle = (string) $th['handle'];
                $pt = Theme::getByHandle($pThemeHandle);
                if (!is_object($pt)) {
                    $pt = Theme::add($pThemeHandle, $pkg);
                }
                if ($xml->getBool($th['activated'])) {
                    $pt->applyToSite();
                }
                if (!empty($th['active-skin'])) {
                    $skin = $pt->getSkinByIdentifier((string) $th['active-skin']);
                    if ($skin) {
                        $app = Facade::getFacadeApplication();
                        $site = $app->make('site')->getSite();
                        $entityManager = $app->make(EntityManager::class);
                        $site->setThemeSkinIdentifier($skin->getIdentifier());
                        $entityManager->persist($site);
                        $entityManager->flush();
                    }
                }
            }
        }
    }

}
