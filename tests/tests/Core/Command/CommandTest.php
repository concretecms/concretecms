<?php
namespace Concrete\Tests\Core\Command;

use Bernard\Envelope;
use Bernard\Normalizer\EnvelopeNormalizer;
use Bernard\Normalizer\PlainMessageNormalizer;
use Concrete\Core\Foundation\Queue\Router\QueuedCommandClassNameRouter;
use Concrete\Core\Foundation\Command\Dispatcher;
use Concrete\Core\Page\Type\Command\UpdatePageTypeDefaultsCommand;
use Concrete\Core\Support\Facade\Facade;

use Concrete\Core\Page\Command\DeletePageCommand;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use League\Tactician\Bernard\QueueableCommand;
use League\Tactician\Bernard\QueueCommand;
use Concrete\Core\Foundation\Queue\Serializer\SerializerManager;
use League\Tactician\Bernard\Receiver\SameBusReceiver;
use Normalt\Normalizer\AggregateNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Concrete\Core\Foundation\Command\DispatcherFactory;
use League\Tactician\CommandBus;
use Bernard\Message;
use Concrete\Core\Page\Command\DeletePageCommandHandler;

class TestCommand implements QueueableCommand
{

    protected $id;

    public function getName()
    {
        return 'default';
    }

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


}

class CommandTest extends ConcreteDatabaseTestCase
{

    protected $fixtures = [];
    protected $tables = ['Queues', 'QueueMessages'];

    public function testCommandSerialize()
    {
        // This is a basic, unqueued command
        $deletePageCommand = new DeletePageCommand(4, 1);

        // Wrap it in the queue command wrapper
        $command = new QueueCommand($deletePageCommand, 'default');
        $envelope = new Envelope($command);

        $app = Facade::getFacadeApplication();
        $serializer = $app->make(SerializerManager::class)->getSerializer();

        $data = $serializer->normalize($envelope);

        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('timestamp', $data);
        $this->assertArrayHasKey('class', $data);
        $this->assertArrayHasKey('class', $data['message']);
        $this->assertEquals('Concrete\Core\Page\Command\DeletePageCommand', $data['message']['class']);

        $string = $serializer->serialize($envelope);

        $command = $serializer->unserialize($string);
        $message = $command->getMessage();

        $this->assertInstanceOf(Message::class, $message);

        $deletePageCommand = $message->getCommand();
        $this->assertInstanceOf(DeletePageCommand::class, $deletePageCommand);
    }


    public function testBus()
    {
        $app = Facade::getFacadeApplication();

        // This is a basic, unqueued command
        $deletePageCommand = new DeletePageCommand(4, 1);

        // Wrap it in the queue command wrapper
        $command = new QueueCommand($deletePageCommand, 'default');

        /**
         * @var $dispatcher Dispatcher
         */
        $dispatcher = $app->make(DispatcherFactory::class)->getDispatcher();
        $bus = $dispatcher->getBus($dispatcher::BUS_TYPE_ASYNC);
        $this->assertInstanceOf(CommandBus::class, $bus);
        $bus->handle($command);
    }

    public function testForcingACommandToQueue()
    {
        $app = Facade::getFacadeApplication();
        $config = $app->make('config');
        $dispatcherFactory = new DispatcherFactory($app, $config);
        $dispatcher = $dispatcherFactory->getDispatcher();

        $command = new DeletePageCommand(1, 4);
        list($type, $queue) = $dispatcher->getBusTypeForCommand($command);

        $this->assertNull($queue);
        $this->assertEquals($dispatcher::BUS_TYPE_SYNC, $type);

        $command = new UpdatePageTypeDefaultsCommand(1, 1, 1, 1, 1);
        list($type, $queue) = $dispatcher->getBusTypeForCommand($command);

        $this->assertEquals('default', $queue);
        $this->assertEquals($dispatcher::BUS_TYPE_ASYNC, $type);

        $dispatcherFactory->registerCommand(DeletePageCommandHandler::class, DeletePageCommand::class, true);

        $command = new DeletePageCommand(1, 4);
        list($type, $queue) = $dispatcherFactory->getDispatcher()->getBusTypeForCommand($command);

        $this->assertEquals('default', $queue);
        $this->assertEquals($dispatcher::BUS_TYPE_ASYNC, $type);
    }
}
