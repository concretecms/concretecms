<?php
namespace Concrete\Core\Notification\Alert\Filter;

use Concrete\Core\Notification\Alert\AlertList;
use Concrete\Core\Notification\Type\TypeInterface;

class StandardFilter implements FilterInterface
{

    /**
     * @var TypeInterface
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $databaseNotificationType;


    /**
     * StandardFilter constructor. The $notificationDatabaseType parameter is the name of the notification
     * discriminator column. It is separate from the key.
     * @param TypeInterface $type
     * @param $key
     * @param $name
     * @param $databaseNotificationType
     */
    public function __construct(TypeInterface $type, $key, $name, $databaseNotificationType)
    {
        $this->type = $type;
        $this->key = $key;
        $this->name = $name;
        $this->databaseNotificationType = $databaseNotificationType;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return TypeInterface
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param TypeInterface $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getDatabaseNotificationType()
    {
        return $this->databaseNotificationType;
    }

    /**
     * @param string $databaseNotificationType
     */
    public function setDatabaseNotificationType($databaseNotificationType)
    {
        $this->databaseNotificationType = $databaseNotificationType;
    }



    public function filterAlertList(AlertList $list)
    {
        $list->getQueryObject()->andWhere('n.type = :databaseNotificationType');
        $list->getQueryObject()->setParameter('databaseNotificationType', $this->getDatabaseNotificationType());
    }


}
