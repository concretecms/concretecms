<?php
namespace Concrete\Controller\Backend\Board\Instance;

use Concrete\Core\Board\Command\ScheduleBoardInstanceRuleCommand;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Board\InstanceSlotRule;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Checker;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class Rules extends AbstractController
{

    /**
     * @var InstanceSlotRule|null
     */
    protected $boardInstanceRule;

    public function update($boardInstanceSlotRuleID)
    {
        $em = $this->app->make(EntityManager::class);
        $error = $this->app->make('error');
        $token = $this->app->make('token');
        if (!$token->validate('save_rule')) {
            $error->add($token->getErrorMessage());
        }

        if ($boardInstanceSlotRuleID) {
            $boardInstanceRule = $em->find(InstanceSlotRule::class, $boardInstanceSlotRuleID);
            if ($boardInstanceRule) {
                $board = $boardInstanceRule->getInstance()->getBoard();
                if ($board) {
                    $permissions = new Checker($board);
                    if ($boardInstanceRule->isLocked()) {
                        $canEditRule = $permissions->canEditBoardLockedRules();
                    } else {
                        $canEditRule = $permissions->canEditBoardContents();
                    }
                    if (!$canEditRule) {
                        $error->add(t('You do not have permission to edit this instance slot rule.'));
                    }
                } else {
                    $error->add(t('Invalid board.'));
                }
            } else {
                $error->add(t('Invalid instance slot rule object.'));
            }
        }

        if (!$error->has()) {
            $data = $this->request->request->all();

            $command = new ScheduleBoardInstanceRuleCommand();
            $command->setBoardInstanceSlotRuleID($boardInstanceSlotRuleID);
            $command->setStartDate($data['startDate']);
            $command->setEndDate($data['endDate']);
            $command->setStartTime($data['startTime']);
            $command->setEndTime($data['endTime']);
            $command->setTimezone($data['timezone']);
            $command->setSlot($data['slot']);

            $rule = $this->app->executeCommand($command);
            return new JsonResponse($rule);

        }

        return new JsonResponse($error);
    }
}
