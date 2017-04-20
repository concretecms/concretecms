<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Feature\Feature;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Category;
use Concrete\Core\User\UserInfo;

class ImportPageStructureRoutine extends AbstractPageStructureRoutine implements SpecifiableHomePageRoutineInterface
{

    protected $home;

    public function getHandle()
    {
        return 'page_structure';
    }

    /**
     * Useful when we're calling this from another routine that imports a new home page.
     * @param $c
     */
    public function setHomePage($c)
    {
        $this->home = $c;
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->pages)) {
            $nodes = array();
            $i = 0;
            foreach ($sx->pages->page as $p) {
                $p->originalPos = $i;
                $nodes[] = $p;
                ++$i;
            }
            usort($nodes, array('static', 'setupPageNodeOrder'));
            $siteTree = null;
            if (isset($this->home)) {
                $home = $this->home;
                $siteTree = $this->home->getSiteTreeObject();
            } else {
                $home = Page::getByID(HOME_CID, 'RECENT');
            }

            foreach ($nodes as $px) {
                $pkg = static::getPackageObject($px['package']);
                $data = array();
                $user = (string) $px['user'];
                if ($user != '') {
                    $ui = UserInfo::getByUserName($user);
                    if (is_object($ui)) {
                        $data['uID'] = $ui->getUserID();
                    } else {
                        $data['uID'] = USER_SUPER_ID;
                    }
                }
                $cDatePublic = (string) $px['public-date'];
                if ($cDatePublic) {
                    $data['cDatePublic'] = $cDatePublic;
                }

                $data['pkgID'] = 0;
                if (is_object($pkg)) {
                    $data['pkgID'] = $pkg->getPackageID();
                }
                $args = array();
                $ct = Type::getByHandle($px['pagetype']);
                $template = Template::getByHandle($px['template']);
                if ($px['path'] != '') {
                    // not home page
                    $page = Page::getByPath($px['path'], 'RECENT', $siteTree);
                    if (!is_object($page) || ($page->isError())) {
                        $lastSlash = strrpos((string) $px['path'], '/');
                        $parentPath = substr((string) $px['path'], 0, $lastSlash);
                        $data['cHandle'] = substr((string) $px['path'], $lastSlash + 1);
                        if (!$parentPath) {
                            $parent = $home;
                        } else {
                            $parent = Page::getByPath($parentPath, 'RECENT', $siteTree);
                        }
                        $page = $parent->add($ct, $data);
                    }
                } else {
                    $page = $home;
                }

                $cName = (string) $px['name'];
                if ($cName) {
                    $args['cName'] = $cName;
                }

                $cDescription = (string) $px['description'];
                if ($cDescription) {
                    $args['cDescription'] = $cDescription;
                }

                if (is_object($ct)) {
                    $args['ptID'] = $ct->getPageTypeID();
                }

                if ($template) {
                    $args['pTemplateID'] = $template->getPageTemplateID();
                }

                if (count($args)) {
                    $page->update($args);
                }
            }
        }
    }
}
