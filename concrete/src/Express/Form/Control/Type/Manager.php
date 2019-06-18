<?php

namespace Concrete\Core\Express\Form\Control\Type;

use Concrete\Core\Application\Application;
use Concrete\Core\Support\Manager as CoreManager;

defined('C5_EXECUTE') or die('Access Denied.');

class Manager extends CoreManager
{
    protected $entityManager;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->driver('entity_property');
        $this->driver('attribute_key');
        $this->driver('association');
    }

    public function createEntityPropertyDriver()
    {
        return $this->app->make(EntityPropertyType::class);
    }

    public function createAttributeKeyDriver()
    {
        return $this->app->make(AttributeKeyType::class);
    }

    public function createAssociationDriver()
    {
        return $this->app->make(AssociationType::class);
    }
}
