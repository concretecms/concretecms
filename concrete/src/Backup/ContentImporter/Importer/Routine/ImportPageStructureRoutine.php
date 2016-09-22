<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Feature\Feature;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Category;
use Concrete\Core\User\UserInfo;

class ImportPageStructureRoutine extends AbstractPageStructureRoutine
{
    public function getHandle()
    {
        return 'page_structure';
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
            $home = Page::getByID(HOME_CID, 'RECENT');

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
                    $page = Page::getByPath($px['path']);
                    if (!is_object($page) || ($page->isError())) {
                        $lastSlash = strrpos((string) $px['path'], '/');
                        $parentPath = substr((string) $px['path'], 0, $lastSlash);
                        $data['cHandle'] = substr((string) $px['path'], $lastSlash + 1);
                        if (!$parentPath) {
                            $parent = $home;
                        } else {
                            $parent = Page::getByPath($parentPath);
                        }
                        $page = $parent->add($ct, $data);
                    }
                } else {
                    $page = $home;
                }

                $args['cName'] = $px['name'];
                $args['cDescription'] = $px['description'];
                if (is_object($ct)) {
                    $args['ptID'] = $ct->getPageTypeID();
                }
                $args['pTemplateID'] = $template->getPageTemplateID();
                $page->update($args);
            }
        }
    }
}
