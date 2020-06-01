<?php
namespace Concrete\Controller\Backend\Board;

use Concrete\Core\Board\Command\PinSlotToBoardCommand;
use Concrete\Core\Board\Command\UnpinSlotFromBoardCommand;
use Concrete\Core\Board\Instance\Slot\RenderedSlotCollectionFactory;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Package\Offline\Exception;
use Concrete\Core\Permission\Checker;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Board\Instance as InstanceEntity;
use Symfony\Component\HttpFoundation\JsonResponse;

class Instance extends AbstractController
{

    public function pinSlot()
    {
        $entityManager = $this->app->make(EntityManager::class);
        $boardInstanceID = $this->request->request->get('boardInstanceID');
        if ($boardInstanceID) {
            $instance = $entityManager->find(InstanceEntity::class, $boardInstanceID);
            if ($instance) {
                $board = $instance->getBoard();
            }
        }

        $checker = new Checker($board);
        if ($board && $checker->canEditBoardContents()) {
            if ($this->request->request->get('action') === 'unpin') {
                $command = new UnpinSlotFromBoardCommand();
            } else {
                $command = new PinSlotToBoardCommand();
                $command->setBlockID($this->request->request->get('bID'));
            }
            $command->setInstance($instance);
            $command->setSlot($this->request->request->get('slot'));
            $this->app->executeCommand($command);

            $renderedSlotCollectionFactory = $this->app->make(RenderedSlotCollectionFactory::class);
            $renderedSlotCollection = $renderedSlotCollectionFactory->createCollection($instance);

            return new JsonResponse($renderedSlotCollection->getRenderedSlot($this->request->request->get('slot')));
        }

        throw new Exception(t('Access Denied.'));
    }
}
