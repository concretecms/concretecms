<?php
namespace Concrete\Controller\Backend;
use Controller;
use Loader;
use stdClass;
use User;
use Page;
use Permissions;

class EditorData extends Controller {

	public function view()
    {
        if (Loader::helper('validation/token')->validate('editor')) {
            $obj = new stdClass;
            $obj->snippets = array();
            $u = new User();
            if ($u->isRegistered()) {
                $snippets = \Concrete\Core\Editor\Snippet::getActiveList();
                foreach($snippets as $sns) {
                    $menu = new stdClass;
                    $menu->scsHandle = $sns->getSystemContentEditorSnippetHandle();
                    $menu->scsName = $sns->getSystemContentEditorSnippetName();
                    $obj->snippets[] = $menu;
                }
            }
            $c = Page::getByID($_REQUEST['cID']);
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
            print Loader::helper('json')->encode($obj);
            exit;
        }
	}
}

