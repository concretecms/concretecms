<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Automation;

use Concrete\Core\Command\Process\Command\ClearProcessDataCommand;
use Concrete\Core\Command\Process\Command\DeleteFailedMessageCommand;
use Concrete\Core\Command\Process\Command\RetryFailedMessageCommand;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Messenger\Transport\FailedTransportManager;
use Concrete\Core\Page\Controller\DashboardPageController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Transport\Receiver\SingleMessageReceiver;
use Symfony\Component\Messenger\Worker;

class Failed extends DashboardPageController
{

    public function view()
    {
        $manager = $this->app->make(FailedTransportManager::class);
        $receiverName = $manager->getDefaultFailedReceiverName();
        $failureTransports = $manager->getReceivers();
        $receiver = $failureTransports->get($receiverName);
        if (!($receiver instanceof ListableReceiverInterface)) {
            throw new \Exception(t('Receiver does not implement the ListableReceiverInterface.'));
        }
        $this->set('receiver', $receiver);
        $messages = iterator_to_array($receiver->all(500));
        $this->set('messages', $messages);
        $this->setThemeViewTemplate('full.php');
    }

    public function delete_message()
    {
        if (!$this->token->validate('delete_message')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $command = new DeleteFailedMessageCommand($this->request->request->get('id'));
            $countRemaining = $this->app->executeCommand($command);
            return new JsonResponse(['count' => $countRemaining]);
        } else {
            return $this->error->createResponse();
        }
    }

    public function retry_message()
    {
        $manager = $this->app->make(FailedTransportManager::class);
        $receiverName = $manager->getDefaultFailedReceiverName();
        if (!$this->token->validate('retry_message')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $command = new RetryFailedMessageCommand($this->request->request->get('id'));
            $countRemaining = $this->app->executeCommand($command);
            return new JsonResponse(['count' => $countRemaining]);
        } else {
            return $this->error->createResponse();
        }
    }


}
