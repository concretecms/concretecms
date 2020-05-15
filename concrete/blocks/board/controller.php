<?php

namespace Concrete\Block\Board;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Board\Command\CreateBoardInstanceCommand;
use Concrete\Core\Board\Instance\Renderer;
use Concrete\Core\Board\Template\TemplateLocator;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Board\Template\TemplateInstance;
use Concrete\Core\Entity\Board\Instance;
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
        return t('Adds a Board to your website.');
    }

    public function getBlockTypeName()
    {
        return t('Board');
    }

    public function add()
    {
        $em = $this->app->make(EntityManager::class);
        $boardSelect = ['' => t('** Choose a Board')];
        $boards = [];
        foreach($em->getRepository(Board::class)->findAll() as $board) {
            $boards[] = $board;
            $boardSelect[$board->getBoardID()] = $board->getBoardName();
        }
        $this->set('boardSelect', $boardSelect);
        $this->set('boards', $boards);
    }

    public function save($args)
    {
        if (!$this->boardInstanceID) {
            // Create a new instance for this board
            $board = $this->app->make(EntityManager::class)
                ->find(Board::class, $args['boardID']);
            if ($board) {
                $command = new CreateBoardInstanceCommand();
                $command->setBoard($board);
                $command->setSite($this->getCollectionObject()->getSite());
                $instance = $this->executeCommand($command);
                $args['boardInstanceID'] = $instance->getBoardInstanceID();
            }
        }
        parent::save($args);
    }

    public function edit()
    {
        $this->add();
    }

    public function view()
    {
        $instance = $this->app->make(EntityManager::class)->find(Instance::class, $this->boardInstanceID);
        if ($instance) {
            $this->set('renderer', $this->app->make(Renderer::class));
            $this->set('instance', $instance);
        }
    }



}
