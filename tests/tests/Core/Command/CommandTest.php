<?php
namespace Concrete\Tests\Core\Command;

use Bernard\Envelope;
use Bernard\Message;
use Concrete\Core\Foundation\Command\AsynchronousBus;
use Concrete\Core\Foundation\Command\Dispatcher;
use Concrete\Core\Foundation\Command\DispatcherFactory;
use Concrete\Core\Foundation\Command\SynchronousBus;
use Concrete\Core\Foundation\Queue\Router\QueuedCommandClassNameRouter;
use Concrete\Core\Foundation\Queue\Serializer\SerializerManager;
use Concrete\Core\Page\Command\DeletePageCommand;
use Concrete\Core\Page\Command\DeletePageCommandHandler;
use Concrete\Core\Page\Type\Command\UpdatePageTypeDefaultsCommand;
use Concrete\Core\Support\Facade\Facade;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use League\Tactician\Bernard\QueueableCommand;
use League\Tactician\Bernard\QueueCommand;
use League\Tactician\CommandBus;

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
        /**
         * @var $dispatcher Dispatcher
         */
        $dispatcher = $app->make(DispatcherFactory::class)->getDispatcher();

        // This is a basic, unqueued command
        $deletePageCommand = new DeletePageCommand(4, 1);
        $bus1 = $dispatcher->getBusForCommand($deletePageCommand);
        $this->assertInstanceOf(SynchronousBus::class, $bus1);

        // Wrap it in the queue command wrapper
        $command = new QueueCommand($deletePageCommand, 'default');

        $bus2 = $dispatcher->getBusForCommand($command);
        $this->assertInstanceOf(AsynchronousBus::class, $bus2);
    }

    public function testForcingACommandToABus()
    {
        $app = Facade::getFacadeApplication();
        $config = $app->make('config');
        $dispatcherFactory = new DispatcherFactory($app, $config);
        $dispatcher = $dispatcherFactory->getDispatcher();

        $command = new UpdatePageTypeDefaultsCommand(1, 1, 1, 1, 1);
        $bus = $dispatcher->getBusForCommand($command);
        $this->assertInstanceOf(AsynchronousBus::class, $bus);
        $command = $dispatcher->wrapCommandForDispatch($command, $bus);
        $this->assertInstanceOf(UpdatePageTypeDefaultsCommand::class, $command);

        $dispatcher->registerCommand(DeletePageCommandHandler::class, DeletePageCommand::class, AsynchronousBus::getHandle());

        $command = new DeletePageCommand(1, 4);
        $bus = $dispatcher->getBusForCommand($command);
        $this->assertInstanceOf(AsynchronousBus::class, $bus);
        $command = $dispatcher->wrapCommandForDispatch($command, $bus);
        $this->assertInstanceOf(QueueCommand::class, $command);
        $this->assertEquals('default', $command->getName());
    }
}
