<?php

namespace Concrete\Block\Board;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Board\Template\TemplateLocator;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Board\Template\TemplateInstance;
use Cookie;
use Doctrine\ORM\EntityManager;
use Session;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController
{
    protected $btInterfaceWidth = 500;
    protected $btInterfaceHeight = 150;
    protected $btTable = 'btBoard';

    public $helpers = ['form'];
    
    public $boardID;
    
    public function getBlockTypeDescription()
    {
        return t('Adds a Board to your websdite.');
    }

    public function getBlockTypeName()
    {
        return t('Board');
    }
    
    public function add()
    {
        $em = $this->app->make(EntityManager::class);
        $boards = ['' => t('** Choose a Board')];
        foreach($em->getRepository(Board::class)->findAll() as $board) {
            $boards[$board->getBoardID()] = $board->getBoardName();    
        }
        $this->set('boards', $boards);
    }
    
    public function edit()
    {
        $this->add();
    }
    
    public function view()
    {
        $template = null;
        $board = null;
        if ($this->boardID) {
            $board = $this->app->make(EntityManager::class)
                ->find(Board::class, $this->boardID);
            if ($board) {
                $template = $board->getTemplate();
                if ($template) {
                    $populator = $this->app->make(Populator::class);
                    $itemCollection = $populator->getItemCollection($board);
                    $locator = $this->app->make(TemplateLocator::class);
                    $this->set('template', new TemplateInstance($itemCollection, $template));
                    $this->set('fileToRender', $locator->getFileToRender($this->getCollectionObject(), $template));
                }
            }
        }
        $this->set('board', $board);
    }



}
