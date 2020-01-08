<?php
namespace Concrete\Controller\Dialog\Block;

use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Block\Command\AddAliasDefaultsBlockCommand;
use Concrete\Core\Block\Command\DefaultsBlockBatchProcessFactory;
use Concrete\Core\Block\Command\UpdateDefaultsBlockCommand;
use Concrete\Core\Block\Command\UpdateForkedAliasDefaultsBlockCommand;
use Concrete\Core\Foundation\Queue\Batch\Processor;
use Concrete\Core\Foundation\Queue\QueueService;
use Concrete\Core\Foundation\Queue\Response\EnqueueItemsResponse;
use Concrete\Core\Http\ResponseFactoryInterface;
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
        $pl->setSiteTreeToAll();
        $pl->ignorePermissions();
        $pl->filterByPageTypeID($ct->getPageTypeID());
        $pl->filterByPageTemplate($template);

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

                    $queue = $this->app->make(QueueService::class);
                    $q = $queue->get('update_defaults');
                    $blocks = $this->block->queueForDefaultsAliasing($_POST);
                    $factory = new DefaultsBlockBatchProcessFactory($b, $c, $this->request->query->get('arHandle'));
                    /**
                     * @var $processor Processor
                     */
                    $processor = $this->app->make(Processor::class);
                    return $processor->process($factory, $blocks);
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
            if (!$r->get('addBlock') && !$r->get('updateForkedBlocks')) {
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
