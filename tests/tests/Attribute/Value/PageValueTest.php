<?php

namespace Concrete\Tests\Attribute\Value;

use Concrete\TestHelpers\Attribute\AttributeValueTestCase;
use Page;

class PageValueTest extends AttributeValueTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tables = array_merge($this->tables, [
            'PageTypes',
            'PageThemes',
            'PermissionAccessEntityTypes',
            'PermissionKeyCategories',
            'PermissionKeys',
            'PageTypePublishTargetTypes',
            'PageThemes',
        ]);

        $this->metadatas = array_merge($this->metadatas, [
            'Concrete\Core\Entity\Attribute\Key\Settings\EmptySettings',
            'Concrete\Core\Entity\Attribute\Value\Value\NumberValue',
            'Concrete\Core\Entity\Page\Template',
        ]);
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        require_once DIR_TESTS . '/assets/Attribute/Value/RelatedPageController.php';
    }

    public function setUp()
    {
        $this->truncateTables();

        parent::setUp();

        $template = \Concrete\Core\Page\Template::add('full', 'Full');
        $pt = \Concrete\Core\Page\Type\Type::add([
            'handle' => 'basic',
            'name' => 'Basic',
        ]);

        $parent = Page::getByID(Page::getHomePageID());

        $parent->add($pt, [
            'cName' => 'Test Page 1',
            'pTemplateID' => $template->getPageTemplateID(),
        ]);

        $parent->add($pt, [
            'cName' => 'Test Page 2',
            'pTemplateID' => $template->getPageTemplateID(),
        ]);
    }

    public function getAttributeKeyHandle()
    {
        return 'related_page';
    }

    public function getAttributeKeyName()
    {
        return 'Page';
    }

    public function createAttributeKeySettings()
    {
        return null;
    }

    public function getAttributeTypeHandle()
    {
        return 'related_page';
    }

    public function getAttributeTypeName()
    {
        return 'Related Page';
    }

    public function getAttributeValueClassName()
    {
        return 'Concrete\Core\Entity\Attribute\Value\Value\NumberValue';
    }

    public function baseAttributeValues()
    {
        return [
            [
                function () {
                    return \Page::getByID(1);
                },
                function () {
                    return \Page::getByID(1);
                },
            ],
        ];
    }

    public function displayAttributeValues()
    {
        return [
            [
                function () {
                    return \Page::getByID(1);
                },
                'Home',
            ],
        ];
    }

    public function plaintextAttributeValues()
    {
        return [
            [
                function () {
                    return \Page::getByID(1);
                },
                'Home',
            ],
        ];
    }

    public function searchIndexAttributeValues()
    {
        return [
            [
                function () {
                    return \Page::getByID(1);
                },
                1,
            ],
        ];
    }
}
