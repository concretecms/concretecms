<?php
namespace tests\Core\Calendar;

use Concrete\Core\Calendar\Event\Event;
use Concrete\Core\Calendar\Event\EventRepetition;
use Concrete\Core\Foundation\Repetition\RepetitionInterface;

/**
 * Class EventTest
 * Test cases for `\Concrete\Core\Calendar\Event\Event`
 *
 * @package tests\Core\Calendar
 */
class EventTest extends \ConcreteDatabaseTestCase
{

    protected $tables = array('CalendarEvents', 'CalendarEventRepetitions');
    protected $fixtures = array();

    /** @var RepetitionInterface */
    protected $repetition;

    public function setUp()
    {
        parent::setUp();

        $mock = $this->getMockBuilder('\Concrete\Core\Foundation\Repetition\RepetitionInterface')
                     ->disableOriginalConstructor()
                     ->getMock();
        $mock->method('getID')->willReturn(1337);

        $this->repetition = $mock;
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->repetition);
    }

    public function testSave()
    {
        $name = md5(uniqid());
        $description = md5(uniqid());

        $event = new Event($name, $description, $this->repetition);
        $event->save();

        $db = \Database::connection();
        $result = $db->query('SELECT * FROM CalendarEvents WHERE eventID=? LIMIT 1', array($event->getID()))->fetch();

        $this->assertNotEmpty((array)$result, 'Failed to retrieve event');
        $this->assertEquals(array_get($result, 'repetitionID'), 1337, 'Failed to validate event repetition');
        $this->assertEquals(array_get($result, 'name'), $name, 'Failed to validate event name');
        $this->assertEquals(array_get($result, 'description'), $description, 'Failed to validate event description');
    }

    public function testUpdate()
    {
        $name = md5(uniqid());
        $description = md5(uniqid());

        $db = \Database::connection();
        $db->insert(
            'CalendarEvents',
            array(
                'name'         => 'test',
                'description'  => 'test',
                'repetitionID' => 1));

        $id = $db->lastInsertId();

        $event = new Event('', '', new EventRepetition());
        $event->setRepetition($this->repetition);
        $event->setName($name);
        $event->setDescription($description);
        $event->setId($id);
        $event->save();

        $result = $db->query('SELECT * FROM CalendarEvents WHERE eventID=?', array($id))->fetch();
        $this->assertNotEmpty((array)$result, 'Failed to retrieve event');
        $this->assertEquals(array_get($result, 'repetitionID'), 1337, 'Failed to validate event repetition');
        $this->assertEquals(array_get($result, 'name'), $name, 'Failed to validate event name');
        $this->assertEquals(array_get($result, 'description'), $description, 'Failed to validate event description');
    }

    public function testUpdateFail()
    {
        $event = new Event('', '', $this->repetition);
        $event->setID('invalid id');

        $this->assertFalse($event->save(), 'Failed to return false for failed update');
    }

    public function testGetByID()
    {
        $name = md5(uniqid());
        $description = md5(uniqid());
        $repetition = new EventRepetition();
        $repetition->save();

        $db = \Database::connection();
        $db->insert(
            'CalendarEvents',
            array(
                'name'         => $name,
                'description'  => $description,
                'repetitionID' => $repetition->getID()));

        $id = $db->lastInsertId();
        $event = Event::getByID($id);

        $this->assertNotNull($event, 'Retrieving event by ID failed.');
        $this->assertEquals(
            $event->getRepetition()->getID(),
            $repetition->getID(),
            'Failed to validate event repetition');
        $this->assertEquals($event->getName(), $name, 'Failed to validate event name');
        $this->assertEquals($event->getDescription(), $description, 'Failed to validate event description');
        $this->assertEquals($event->getID(), $id, 'Failed to validate event identifier');

        // Test with string id
        $event = Event::getByID((string)$id);

        $this->assertNotNull($event, 'Retrieving event by string ID failed.');
        $this->assertEquals(
            $event->getRepetition()->getID(),
            $repetition->getID(),
            'Failed to validate event repetition');
        $this->assertEquals($event->getName(), $name, 'Failed to validate event name');
        $this->assertEquals($event->getDescription(), $description, 'Failed to validate event description');
        $this->assertEquals($event->getID(), $id, 'Failed to validate event identifier');
    }

    public function testGetByInvalidID()
    {
        $event = Event::getByID('Invalid ID');
        $this->assertNull($event, 'Failed to return null on invalid event ID');
    }

    public function testDelete()
    {
        $db = \Database::connection();
        $db->insert(
            'CalendarEvents',
            array(
                'name'         => 'test',
                'description'  => 'test',
                'repetitionID' => 1));
        $id = $db->lastInsertId();

        $event = new Event('', '', $this->repetition);
        $event->setID($id);

        $this->assertTrue($event->delete(), 'Failed to delete');
        $result = $db->query('SELECT count(eventID) count FROM CalendarEvents WHERE eventID=?', array($id))->fetch();

        $this->assertEquals(0, array_get($result, 'count'), 'Failed to delete, yet got success');

        $event = new Event('test', 'test', $this->repetition);
        $this->assertFalse($event->delete(), 'Deleting invalid event returned success');

        $event->setId('Invalid ID');
        $this->assertFalse($event->delete(), 'Deleting event with invalid id returned success');
    }

}
