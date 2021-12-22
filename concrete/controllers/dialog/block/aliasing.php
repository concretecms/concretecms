<?php
namespace Concrete\Controller\Dialog\Block;

use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\Command\AddAliasDefaultsBlockCommand;
use Concrete\Core\Block\Command\ForceDefaultDisplayOrderBlockCommand;
use Concrete\Core\Block\Command\UpdateForkedAliasDefaultsBlockCommand;
use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Checker;

class Aliasing extends BackendInterfaceBlockController
{
    protected $viewPath = '/dialogs/block/aliasing';

    public function view()
    {
        $ct = Type::getByDefaultsPage($this->page);
        $template = Template::getByID($this->page->getPageTemplateID());

        $pl = new PageList();
        $pl->setSiteTreeToAll();
        $pl->ignorePermissions();
        $pl->filterByPageTypeID($ct->getPageTypeID());
        $pl->filterByPageTemplate($template);

        $this->set('total', $pl->getTotalResults());
    }

    /**
     * @return \Concrete\Core\Http\Response | void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException | \Doctrine\DBAL\Exception
     */
    public function submit()
    {
        if ($this->validateAction() && $this->canAccess()) {
            $a = Area::get($this->page, $this->request->get('arHandle'));
            $c = $this->page;
            if (is_object($a)) {
                $b = Block::getByID($this->request->get('bID'), $c, $a);
                $p = new Checker($b);
                /** @phpstan-ignore-next */
                if ($p->canAdminBlock() && $c->isMasterCollection()) {
                    $blocks = $this->block->queueForDefaultsAliasing($this->request->request->get('addBlock'), $this->request->request->get('updateForkedBlocks'), (bool) $this->request->request->get('forceDisplayOrder' , false));
                    $batch = Batch::create(t('Update Defaults'), function() use ($blocks) {
                        foreach ($blocks as $b) {
                            if ($b['action'] == 'update_forked_alias') {
                                $commandClass = UpdateForkedAliasDefaultsBlockCommand::class;
                            } elseif ($b['action']== 'force_display_order') {
                                $commandClass = ForceDefaultDisplayOrderBlockCommand::class;
                            } else {
                                $commandClass = AddAliasDefaultsBlockCommand::class;
                            }

                            $command = new $commandClass(
                                $this->block->getBlockID(),
                                $this->page->getCollectionID(),
                                $this->page->getVersionID(),
                                $this->area->getAreaHandle(),
                                $b['bID'],
                                $b['cID'],
                                $b['cvID'],
                                $b['arHandle'],
                                (bool) $this->request->request->get('forceDisplayOrder', false)
                            );
                            yield $command;
                        }
                    });
                    return $this->dispatchBatch($batch);
                }
            }
        }

        if ($this->error->has()) {
            return $this->app->make(ResponseFactoryInterface::class)->error($this->error);
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

    protected function validateAction()
    {
        if (parent::validateAction()) {
            $r = $this->request->request;
            if (!$r->get('addBlock') && !$r->get('updateForkedBlocks') && !$r->get('forceDisplayOrder')) {
                $this->error->add(t('You need to select at least one action'));
            } else {
                return true;
            }
        }

        return false;
    }

    protected function canAccess()
    {
        return $this->permissions->canAdminBlock();
    }
}
