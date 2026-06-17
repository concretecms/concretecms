<?php

namespace Concrete\TestHelpers\Attribute;

use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Core\Support\Facade\Application as ApplicationFacade;
use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;

abstract class AttributeTypeTestCase extends ConcreteDatabaseTestCase
{
    /**
     * {@inheritdoc}
     *
     * @see ConcreteDatabaseTestCase::$entityClassNames
     */
    protected $entityClassNames = [
        'Concrete\Core\Entity\Site\Type',
        'Concrete\Core\Entity\Site\Site',
        'Concrete\Core\Entity\Site\Locale',
        'Concrete\Core\Entity\Site\Tree',
        'Concrete\Core\Entity\Site\SiteTree',
        'Concrete\Core\Entity\Attribute\Category',
        'Concrete\Core\Entity\Summary\Category',
        'Concrete\Core\Entity\Page\Summary\PageTemplate',
        'Concrete\Core\Entity\Attribute\Key\Key',
        'Concrete\Core\Entity\Attribute\Key\PageKey',
        'Concrete\Core\Entity\Attribute\Type',
        'Concrete\Core\Entity\Attribute\Value\Value\TextValue',
        'Concrete\Core\Entity\Attribute\Value\Value\BooleanValue',
        'Concrete\Core\Entity\Attribute\Value\Value\Value',
        'Concrete\Core\Entity\User\User',
        'Concrete\Core\Entity\User\UserSignup',
        'Concrete\Core\Entity\Attribute\Value\PageValue',
        'Concrete\Core\Entity\Attribute\Key\UserKey',
    ];

    /**
     * {@inheritdoc}
     *
     * @see ConcreteDatabaseTestCase::$tables
     */
    protected $tables = [
        'Collections',
        'Pages',
        'PageSearchIndex',
        'PageTypes',
        'CollectionVersions',
    ];

    /** @var string */
    protected $atHandle;

    /** @var Concrete\Core\Entity\Attribute\Type */
    protected $at;

    /** @var Concrete\Core\Attribute\Controller */
    protected $ctrl;

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    public function setUp(): void
    {
        // Truncate tables
        $this->truncateTables();

        parent::setUp();

        $app = ApplicationFacade::getFacadeApplication();
        $service = $app->make('site');
        if (!$service->getDefault()) {
            $service->installDefault('en_US');
        }

        $this->at = AttributeType::add($this->atHandle, $this->atHandle);
        $this->ctrl = $this->at->getController();

        Category::add('collection');
        $key = CollectionKey::add($this->at, ['akHandle' => 'test', 'akName' => 'Test']);
        $this->ctrl->setAttributeKey($key);
    }
}
