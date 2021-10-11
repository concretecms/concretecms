<?php
namespace Concrete\Controller\Backend\Board\Instance\Rules;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Entity\Board\InstanceSlotRule;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Checker;
use Concrete\Core\View\View;
use Doctrine\ORM\EntityManager;

class Preview extends Controller
{

    protected $viewPath = '/backend/board/instance/rules/preview';

    public function __construct()
    {
        parent::__construct();

        $this->view = new View($this->viewPath);
        $this->view->setViewTemplate('block_preview.php');
    }

    public function view($boardInstanceSlotRuleID)
    {
        $valid = false;
        $entityManager = $this->app->make(EntityManager::class);
        $rule = $entityManager->find(InstanceSlotRule::class, $boardInstanceSlotRuleID);
        if ($rule) {
            $instance = $rule->getInstance();
            if ($instance) {
                $board = $instance->getBoard();
                if ($board) {
                    $checker = new Checker($board);
                    if ($checker->canEditBoardContents()) {
                        $valid = true;
                    }
                }
            }
            if ($valid) {
                $block = Block::getByID($rule->getBlockID());
                if ($block) {
                    $blockView = new BlockView($block);
                    $this->set('blockView', $blockView);
                    $theme = Theme::getSiteTheme();
                    $this->setTheme($theme);
                }
            } else {
                throw new \RuntimeException('Access Denied.');
            }
        }
    }




}
