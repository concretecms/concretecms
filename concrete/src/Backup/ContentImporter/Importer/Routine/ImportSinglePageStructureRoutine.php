<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Page\Single;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Xml;

class ImportSinglePageStructureRoutine extends AbstractRoutine implements SpecifiableHomePageRoutineInterface
{
    public function getHandle()
    {
        return 'single_pages';
    }

    public function setHomePage($page)
    {
        $this->home = $page;
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->singlepages)) {
            $app = Application::getFacadeApplication();
            $xml = $app->make(Xml::class);
            $defaultSite = $app->make('site')->getDefault();
            foreach ($sx->singlepages->page as $p) {
                $pkg = static::getPackageObject($p['package']);

                if ($xml->getBool($p['global'])) {
                    $spl = Single::addGlobal($p['path'], $pkg);
                } else {
                    $root = $xml->getBool($p['root']);

                    $siteTree = null;
                    if (isset($this->home)) {
                        $siteTree = $this->home->getSiteTreeObject();
                    } else {
                        $home = \Page::getByID(\Page::getHomePageID());
                        $siteTree = $home->getSiteTreeObject();
                    }

                    if ($siteTree === null) {
                        $siteTree = $defaultSite;
                    }

                    $spl = Single::createPageInTree($p['path'], $siteTree, $root, $pkg);
                }

                if (is_object($spl)) {
                    if ($p['name']) {
                        $spl->update(array('cName' => $p['name'], 'cDescription' => $p['description']));
                    }

                    if ($p['custom-path']) {
                        $spl->setCanonicalPagePath((string) $p['custom-path'], false);
                    }
                }
            }
        }
    }
}
