<?php
namespace Concrete\Core\Summary\Category\Driver;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Doctrine\ORM\EntityManager;

defined('C5_EXECUTE') or die("Access Denied.");

abstract class AbstractDriver implements DriverInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

}
