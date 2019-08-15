<?php
namespace Concrete\Controller\Backend;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Legacy\Loader;
use stdClass;
use Concrete\Core\User\User as ConcreteUser;
use Concrete\Core\Page\Page as ConcretePage;
use Concrete\Core\Permission\Checker as Permissions;

class EditorData extends Controller
{
    public function view()
    {
        if (Loader::helper('validation/token')->validate('editor')) {
            $obj = new stdClass();
            $obj->snippets = array();
            $u = new ConcreteUser();
            if ($u->isRegistered()) {
                $snippets = \Concrete\Core\Editor\Snippet::getActiveList();
                foreach ($snippets as $sns) {
                    $menu = new stdClass();
                    $menu->scsHandle = $sns->getSystemContentEditorSnippetHandle();
                    $menu->scsName = $sns->getSystemContentEditorSnippetName();
                    $obj->snippets[] = $menu;
                }
            }
            $c = ConcretePage::getByID($_REQUEST['cID']);
            $obj->classes = array();
            if (is_object($c) && !$c->isError()) {
                $cp = new Permissions($c);
                if ($cp->canViewPage()) {
                    $pt = $c->getCollectionThemeObject();
                    if (is_object($pt)) {
                        $obj->classes = $pt->getThemeEditorClasses();
                    }
                }
            }
            echo Loader::helper('json')->encode($obj);
            exit;
        }
    }
}
