<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Board\Designer\Command\ScheduleCustomElementCommand;
use Concrete\Core\Board\Designer\Command\SetCustomElementItemsCommand;
use Concrete\Core\Entity\Board\Designer\CustomElement;
use Concrete\Core\Entity\Board\Designer\CustomElementItem;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceSlotRule;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Permission\Checker;
use Symfony\Component\HttpFoundation\JsonResponse;

class Scheduler extends DashboardSitePageController
{

    public function view($id = null)
    {
        $element = $this->getCustomElement($id);
        if (is_object($element)) {
            $this->set('selectedElementID', $element->getID());
        } else {
            $this->set('selectedElementID', 0);
        }
        $instances = [];
        $r = $this->entityManager->getRepository(Instance::class);
        foreach ($r->findAll() as $instance) {
            $permissions = new Checker($instance->getBoard());
            if ($permissions->canEditBoardContents()) {
                $instances[] = $instance;
            }
        }
        $this->set('elements', $this->entityManager->getRepository(CustomElement::class)->findBy([
            'status' => CustomElement::STATUS_READY_TO_PUBLISH
        ], ['elementName' => 'asc']));
        $this->set('instances', $instances);
        $this->set('date', new Date());
    }

    /**
     * @param $id
     * @return CustomElement
     */
    protected function getCustomElement($id)
    {
        $r = $this->entityManager->getRepository(CustomElement::class);
        $element = $r->findOneById($id);
        return $element;
    }

    /**
     * Returns a JSON encoded list of all rules that are shared across all the selected instances.
     * If a single instance is sent to the server, we're just getting a full list of that instances
     * rules.
     */
    public function get_shared_rules()
    {
        if (!$this->token->validate('get_shared_rules')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $instances = [];
            $instanceRepository = $this->entityManager->getRepository(Instance::class);
            foreach ((array)$this->request->request->get('instances') as $instanceID) {
                $instance = $instanceRepository->find($instanceID);
                if ($instance) {
                    $instances[] = $instance;
                }
            }
            $r = $this->entityManager->getRepository(InstanceSlotRule::class);
            $rules = $r->findByMultipleInstances($instances);
            return new JsonResponse($rules);
        }
        $this->view();
    }

    public function submit()
    {
        $element = $this->getCustomElement($this->request->request->get('elementId'));
        if (is_object($element)) {
            $this->set('element', $element);
        }
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {

            $command = new ScheduleCustomElementCommand($element);
            $instances = [];
            foreach((array) $this->request->request->get('instances') as $instanceId) {
                $instance = $this->entityManager->find(Instance::class, $instanceId);
                if ($instance) {
                    $board = $instance->getBoard();
                    $checker = new Checker($board);
                    if ($checker->canEditBoardSettings()) {
                        $instances[] = $instance;
                    }
                }
            }
            $command->setInstances($instances);
            $command->setSlot((int) $this->request->request->get('slot'));
            $command->setLockType($this->request->request->get('lockType'));
            $command->setTimezone($this->request->request->get('timezone'));
            $startDateTime = $this->request->request->get('startDate') . ' ' . $this->request->request->get('startTime');
            $endDateTime = $this->request->request->get('endDate') . ' ' . $this->request->request->get('endTime');
            $command->setStartDateTime($startDateTime);
            $command->setEndDateTime($endDateTime);

            $this->app->executeCommand($command);

            $this->flash('success', t('Board element scheduled successfully.'));
            return new JsonResponse([]);
        }

    }



}
