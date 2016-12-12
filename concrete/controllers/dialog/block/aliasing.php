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
        $this->set('total', $pl->getTotalResults());
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
                    $name = sprintf('update_defaults_%s', $b->getBlockID());
                    $queue = \Queue::get($name);

                    if ($_POST['process']) {
                        $obj = new \stdClass();
                        $messages = $queue->receive(20);
                        foreach ($messages as $key => $p) {
                            $record = unserialize($p->body);

                            $page = \Page::getByID($record['cID'], $record['cvID']);
                            if ($record['action'] == 'add_alias') {
                                $this->block->alias($page);
                            } else if ($record['action'] == 'update_forked_alias') {
                                $forked = \Block::getByID($record['bID'], $page, $record['arHandle']);
                                if (is_object($forked) && !$forked->isError()) {
                                    // take the current block that is in defaults, and replace the block on the page
                                    // with that block.
                                    $existingDisplayOrder = $forked->getBlockDisplayOrder();
                                    $bt = $b->getBlockTypeObject();

                                    // Now we delete the existing forked block.
                                    $forked->deleteBlock();

                                    if ($bt->isCopiedWhenPropagated()) {
                                        $b = $this->block->duplicate($page, true);
                                    } else {
                                        $this->block->alias($page);
                                        $b = \Block::getByID($this->block->getBlockID(), $page, $record['arHandle']);
                                    }

                                    $b->setAbsoluteBlockDisplayOrder($existingDisplayOrder);
                                    $page->rescanDisplayOrder($record['arHandle']);
                                }
                            }
                            $queue->deleteMessage($p);

                        }
                        $obj->totalItems = $queue->count();
                        if ($queue->count() == 0) {
                            $queue->deleteQueue($name);
                        }
                        $obj->bID = $b->getBlockID();
                        $obj->aID = $a->getAreaID();
                        $obj->message = t('All child blocks updated successfully.');
                        echo json_encode($obj);
                        $this->app->shutdown();
                    } else {
                        $queue = $this->block->queueForDefaultsAliasing($_POST['addBlock'], $queue);
                    }

                    $totalItems = $queue->count();
                    \View::element('progress_bar', array('totalItems' => $totalItems, 'totalItemsSummary' => t2("%d pages", "%d pages", $totalItems)));
                }
            }
        }
        $this->app->shutdown();
    }

    /*
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
    */

    protected function canAccess()
    {
        return $this->permissions->canAdminBlock();
    }
}
