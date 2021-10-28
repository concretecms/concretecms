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

        $data = $this->request->request->all();

        if ($boardInstanceSlotRuleID) {
            $boardInstanceRule = $em->find(InstanceSlotRule::class, $boardInstanceSlotRuleID);
            if ($boardInstanceRule) {
                $instance = $boardInstanceRule->getInstance();
                if ($instance) {
                    $permissions = new Checker($instance);
                    $canEditRule = $permissions->canEditBoardInstanceSlot((int) $data['slot']);
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
            $command = new ScheduleBoardInstanceRuleCommand();
            $command->setBoardInstanceSlotRuleID($boardInstanceSlotRuleID);
            $command->setStartDate($data['startDate']);
            $command->setName($data['name']);
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
