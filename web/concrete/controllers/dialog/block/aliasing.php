<?php
namespace Concrete\Controller\Dialog\Block;

use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Page\EditResponse;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Type\Type;

class Aliasing extends BackendInterfaceBlockController
{
    protected $viewPath = '/dialogs/block/aliasing';

    public function view()
    {
        $ct = Type::getByDefaultsPage($this->page);
        $template = Template::getByID($this->page->getPageTemplateID());


        $pl = new PageList();
        $pl->filterByPageTypeID($ct->getPageTypeID());
        $pl->filterByPageTemplate($template);
        $pl->ignorePermissions();
        $pages = $pl->getResults();
        $this->set('cList', $pages);
    }

    public function submit()
    {
        if ($this->validateAction() && $this->canAccess()) {
            $a = \Area::get($this->page, $_GET['arHandle']);
            $c = $this->page;
            if (is_object($a)) {
                $b = \Block::getByID($_GET['bID'], $c, $a);
                $p = new \Permissions($b);
                if ($p->canAdminBlock() && $c->isMasterCollection()) {
                    if (is_array($_POST['cIDs'])) {
                        foreach ($_POST['cIDs'] as $cID) {
                            $nc = \Page::getByID($cID);
                            if (!$b->isAlias($nc)) {
                                $bt = $b->getBlockTypeObject();
                                if ($bt->isCopiedWhenPropagated()) {
                                    $b->duplicate($nc, true);
                                } else {
                                    $b->alias($nc);
                                }
                            }
                        }
                    }
                    // now remove any items that WERE checked and now aren't
                    if (is_array($_POST['checkedCIDs'])) {
                        foreach ($_POST['checkedCIDs'] as $cID) {
                            if (!(is_array($_POST['cIDs'])) || (!in_array($cID, $_POST['cIDs']))) {
                                $nc = \Page::getByID($cID, 'RECENT');
                                $nb = \Block::getByID($_GET['bID'], $nc, $a);
                                if (is_object($nb) && (!$nb->isError())) {
                                    $nb->deleteBlock();
                                }
                                $nc->rescanDisplayOrder($_REQUEST['arHandle']);
                            }

                        }
                    }
                    $er = new EditResponse();
                    $er->setPage($this->page);
                    $er->setAdditionalDataAttribute('bID', $b->getBlockID());
                    $er->setAdditionalDataAttribute('aID', $a->getAreaID());
                    $er->setAdditionalDataAttribute('arHandle', $a->getAreaHandle());
                    $er->setMessage(t('Defaults updated.'));
                    $er->outputJSON();
                }
            }
        }
    }

    protected function canAccess()
    {
        return $this->permissions->canAdminBlock();
    }

}

