<?php

class PageValueTest extends \AttributeValueTestCase
{

    protected function setUp()
    {

        require_once __DIR__ . '/fixtures/RelatedPageController.php';

        $this->tables = array_merge($this->tables, array(
            'PageTypes',
            'PermissionAccessEntityTypes',
            'PermissionKeyCategories',
            'PermissionKeys',
            'PageTypePublishTargetTypes',
            'PageThemes',
        ));

        $this->metadatas = array_merge($this->metadatas, array(
            'Concrete\Core\Entity\Attribute\Key\Settings\EmptySettings',
            'Concrete\Core\Entity\Attribute\Value\Value\NumberValue',
            'Concrete\Core\Entity\Page\Template',
        ));

        parent::setUp();

        $template = \Concrete\Core\Page\Template::add('full', 'Full');
        $pt = \Concrete\Core\Page\Type\Type::add(array(
            'handle' => 'basic',
            'name' => 'Basic',
        ));

        $parent = Page::getByID(HOME_CID);

        $parent->add($pt, array(
            'cName' => 'Test Page 1',
            'pTemplateID' => $template->getPageTemplateID(),
        ));

        $parent->add($pt, array(
            'cName' => 'Test Page 2',
            'pTemplateID' => $template->getPageTemplateID(),
        ));
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
        return array(
            array(
                function() {
                    return \Page::getByID(1);
                },
                function() {
                    return \Page::getByID(1);
                },
            )
        );
    }

    public function displayAttributeValues()
    {
        return array(
            array(
                function() {
                    return \Page::getByID(1);
                },
                'Home',
            )
        );
    }

    public function plaintextAttributeValues()
    {
        return array(
            array(
                function() {
                    return \Page::getByID(1);
                },
                'Home',
            )
        );
    }

    public function searchIndexAttributeValues()
    {
        return array(
            array(
                function() {
                    return \Page::getByID(1);
                },
                1,
            )
        );
    }



}
