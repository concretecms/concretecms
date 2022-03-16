<?php

namespace Concrete\Block\Board;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Board\Command\CreateBoardInstanceCommand;
use Concrete\Core\Board\Command\RegenerateBoardInstanceCommand;
use Concrete\Core\Board\Instance\Renderer;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Permission\Checker;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController
{
    /**
     * @var string[]
     */
    public $helpers = ['form', 'validation/token'];

    /**
     * @var int|null
     */
    public $boardID;

    /**
     * @var int|null
     */
    public $boardInstanceID;

    /**
     * @var int
     */
    protected $btInterfaceWidth = 500;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 500;

    /**
     * @var string
     */
    protected $btTable = 'btBoard';

    /**
     * @var bool
     */
    protected $btIgnorePageThemeGridFrameworkContainer = true;

    /**
     * @var bool
     */
    protected $btSupportsInlineEdit = true;

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Adds a Board to your website.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Board');
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function add()
    {
        $em = $this->app->make(EntityManager::class);
        $boardSelect = ['0' => t('** Choose a Board')];
        $boards = [];
        foreach ($em->getRepository(Board::class)->findAll() as $board) {
            $checker = new Checker($board);
            /** @phpstan-ignore-next-line */
            if ($checker->canViewBoard()) {
                $boards[] = $board;
                $boardSelect[$board->getBoardID()] = $board->getBoardName();
            }
        }
        $this->set('boardID', 0);
        $this->set('boardSelect', $boardSelect);
        $this->set('boards', $boards);
    }

    /**
     * @param \SimpleXMLElement $blockNode
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function export(\SimpleXMLElement $blockNode)
    {
        $data = $blockNode->addChild('data');
        if ($this->boardInstanceID) {
            $instance = $this->app->make(EntityManager::class)->find(Instance::class, $this->boardInstanceID);
            if ($instance) {
                $data->addChild('board', $instance->getBoard()->getBoardName());
            }
        }
    }

    /**
     * @param \SimpleXMLElement $blockNode
     * @param \Concrete\Core\Page\Page $page
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return array<string, mixed>
     */
    public function getImportData($blockNode, $page)
    {
        $args = [];
        $boardName = (string) $blockNode->data->board;
        if ($boardName) {
            $board = $this->app->make(EntityManager::class)->getRepository(Board::class)
                ->findOneByBoardName($boardName)
            ;
            if ($board) {
                $instances = $board->getInstances();
                if (empty($instances[0])) {
                    $command = new CreateBoardInstanceCommand();
                    $command->setBoard($board);
                    $command->setSite($this->app->make('site')->getSite());
                    $instance = $this->app->executeCommand($command);
                } else {
                    $instance = $instances[0];
                }
                $args['boardInstanceID'] = $instance->getBoardInstanceID();
            }
        }

        return $args;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return mixed
     */
    public function action_get_instances()
    {
        $boardID = (int) $this->request->request->get('boardID');
        $instances = [];
        if ($boardID) {
            $board = $this->app->make(EntityManager::class)->find(Board::class, $boardID);
            if ($board) {
                $checker = new Checker($board);
                /** @phpstan-ignore-next-line */
                if ($checker->canViewBoard()) {
                    $instances = $board->getInstances();
                }
            }
        }
        /** @var JsonSerializer $serializer */
        $serializer = $this->app->make(JsonSerializer::class);

        return $serializer->serialize($instances, 'json');
    }

    /**
     * Validate this block on save.
     *
     * @param array<string, mixed>|string|null $args
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     *
     * @return bool|ErrorList
     */
    public function validate($args)
    {
        $e = parent::validate($args);

        $newInstance = $args['newInstance'] ?? false;
        $boardInstanceId = $args['boardInstanceID'] ?? null;
        $board = null;

        // If we're creating a new instance we don't really care what the old instance would be.
        if (!$newInstance) {
            if ($boardInstanceId) {
                $board = $this->app->make(EntityManager::class)
                    ->find(Instance::class, $boardInstanceId)
                ;
            }

            // If we're not making a new instance and we don't have an instance here we've been given an invalid ID
            if (!$board) {
                $e->add(t('Invalid board instance.'));
            }
        }

        return $e;
    }

    /**
     * @param array<string,mixed> $args
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function save($args)
    {
        if (!empty($args['newInstance'])) {
            // Create a new instance for this board
            $board = $this->app->make(EntityManager::class)
                ->find(Board::class, $args['boardID'])
            ;
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

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return JsonResponse
     */
    public function action_regenerate()
    {
        if ($this->boardInstanceID) {
            if ($this->app->make('token')->validate('regenerate')) {
                $instance = $this->app->make(EntityManager::class)
                    ->find(Instance::class, $this->boardInstanceID)
                ;
                if ($instance) {
                    $board = $instance->getBoard();
                    $checker = new Checker($board);
                    /** @phpstan-ignore-next-line */
                    if ($checker->canEditBoardContents()) {
                        $command = new RegenerateBoardInstanceCommand();
                        $command->setInstance($instance);
                        $this->app->executeCommand($command);

                        return new JsonResponse($instance);
                    }
                        throw new \RuntimeException(t('Access Denied.'));
                }
            }
            throw new \RuntimeException(t('Access Denied.'));
        }
        throw new \RuntimeException(t('Invalid board instance.'));
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function edit()
    {
        $this->add();

        if ($this->boardInstanceID) {
            $instance = $this->app->make(EntityManager::class)
                ->find(Instance::class, $this->boardInstanceID)
            ;
            if ($instance) {
                $renderer = $this->app->make(Renderer::class);
                $renderer->setEnableEditing(true);
                $this->set('renderer', $renderer);
                $this->set('boardID', $instance->getBoard()->getBoardID());
                $this->set('instance', $instance);
                $this->set('token', $this->app->make('token'));
            }
        }
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function view()
    {
        if ($this->boardInstanceID) {
            $instance = $this->app->make(EntityManager::class)->find(Instance::class, $this->boardInstanceID);
            if ($instance) {
                $this->set('renderer', $this->app->make(Renderer::class));
                $this->set('instance', $instance);
            }
        }
    }
}
