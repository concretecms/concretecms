<?php
namespace Concrete\Core\Express\Controller;

use Concrete\Core\Application\Application;
use Concrete\Core\Express\Entry\Manager as EntryManager;
use Concrete\Core\Express\Entry\Notifier\NotificationProviderInterface;
use Concrete\Core\Express\Entry\Notifier\StandardNotifier;
use Concrete\Core\Express\Form\Processor\StandardProcessor;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class StandardController implements ControllerInterface
{

    protected $app;
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getContextRegistry()
    {
        return null;
    }

    public function getFormProcessor()
    {
        return $this->app->make(StandardProcessor::class);
    }

    public function getEntryManager(Request $request)
    {
        $manager = $this->app->make(EntryManager::class, ['request' => $request]);
        $manager->setLogger(
            $this->app->make(LoggerFactory::class)->createLogger(Channels::CHANNEL_EXPRESS)
        );
        return $manager;
    }

    public function getNotifier(?NotificationProviderInterface $provider = null)
    {
        /**
         * @var $notifier StandardNotifier
         */
        $notifier = $this->app->make(StandardNotifier::class);
        if ($provider) {
            foreach($provider->getNotifications() as $notification) {
                $notifier->getNotificationList()->addNotification($notification);
            }
        }
        return $notifier;
    }



}
