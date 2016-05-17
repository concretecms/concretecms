<?php
namespace Concrete\Block\DesktopWaitingForMe;

defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Block\BlockController;
use Concrete\Core\Workflow\Progress\Category;
use Core;

class Controller extends BlockController
{
    public $helpers = array('form');

    protected $btInterfaceWidth = 450;
    protected $btInterfaceHeight = 560;

    public function getBlockTypeDescription()
    {
        return t("Displays workflow actions waiting for you.");
    }

    public function getBlockTypeName()
    {
        return t("Waiting for Me");
    }

    public function view()
    {
        $categories = Category::getList();
        $items = [];
        foreach($categories as $category) {
            $list = $category->getPendingWorkflowProgressList();
            if (is_object($list)) {
                foreach($list->get() as $it) {
                    $wp = $it->getWorkflowProgressObject();
                    $wf = $wp->getWorkflowObject();
                    if ($wf->canApproveWorkflowProgressObject($wp)) {
                        $items[] = $wp;
                    }
                }
            }
        }
        $this->set('items', $items);

    }

}
