<?php

namespace Concrete\Block\Board;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Board\Command\CreateBoardInstanceCommand;
use Concrete\Core\Board\Command\RegenerateBoardInstanceCommand;
use Concrete\Core\Board\Instance\Renderer;
use Concrete\Core\Board\Template\TemplateLocator;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Board\Template\TemplateInstance;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Permission\Checker;
use Cookie;
use Doctrine\ORM\EntityManager;
use Session;
use Symfony\Component\HttpFoundation\JsonResponse;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController
{
    protected $btInterfaceWidth = 500;
    protected $btInterfaceHeight = 500;
    protected $btTable = 'btBoard';
    protected $btIgnorePageThemeGridFrameworkContainer = true;
    protected $btSupportsInlineEdit = true;

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
        $boardSelect = ['0' => t('** Choose a Board')];
        $boards = [];
        foreach($em->getRepository(Board::class)->findAll() as $board) {
            $checker = new Checker($board);
            if ($checker->canViewBoard()) {
                $boards[] = $board;
                $boardSelect[$board->getBoardID()] = $board->getBoardName();
            }
        }
        $this->set('boardID', 0);
        $this->set('boardSelect', $boardSelect);
        $this->set('boards', $boards);
    }

    public function action_get_instances()
    {
        $boardID = (int) $this->request->request->get('boardID');
        $instances = [];
        if ($boardID) {
            $board = $this->app->make(EntityManager::class)->find(Board::class, $boardID);
            if ($board) {
                $checker = new Checker($board);
                if ($checker->canViewBoard()) {
                    $instances = $board->getInstances();
                }
            }
        }
        $serializer = $this->app->make(JsonSerializer::class);
        return $serializer->serialize($instances, 'json');
    }

    public function save($args)
    {
        if ($args['newInstance']) {
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

    public function action_regenerate()
    {
        if ($this->app->make('token')->validate('regenerate')) {
            $instance = $this->app->make(EntityManager::class)
                ->find(Instance::class, $this->boardInstanceID);
            if ($instance) {
                $board = $instance->getBoard();
                $checker = new Checker($board);
                if ($checker->canEditBoardContents()) {
                    $command = new RegenerateBoardInstanceCommand();
                    $command->setInstance($instance);
                    $this->app->executeCommand($command);
                    return new JsonResponse($instance);
                } else {
                    throw new \RuntimeException(t('Access Denied.'));
                }
            }
        }
        throw new \RuntimeException(t('Access Denied.'));
    }

    public function edit()
    {
        $this->add();
        $instance = $this->app->make(EntityManager::class)
            ->find(Instance::class, $this->boardInstanceID);
        if ($instance) {
            $renderer = $this->app->make(Renderer::class);
            $renderer->setEnableEditing(true);
            $this->set('renderer', $renderer);
            $this->set('boardID', $instance->getBoard()->getBoardID());
            $this->set('instance', $instance);
            $this->set('token', $this->app->make('token'));
        }
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
