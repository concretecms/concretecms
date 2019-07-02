<?php
namespace Concrete\Tests\Attribute\Value;

use Concrete\TestHelpers\Attribute\AttributeValueTestCase;

class AddressValueTest extends AttributeValueTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tables = array_merge($this->tables, []);

        $this->metadatas = array_merge($this->metadatas, [
            'Concrete\Core\Entity\Attribute\Key\Settings\AddressSettings',
            'Concrete\Core\Entity\Attribute\Value\Value\AddressValue',
        ]);
    }

    public function getAttributeKeyHandle()
    {
        return 'addres';
    }

    public function getAttributeKeyName()
    {
        return 'Address';
    }

    public function createAttributeKeySettings()
    {
        return null;
    }

    public function getAttributeTypeHandle()
    {
        return 'address';
    }

    public function getAttributeTypeName()
    {
        return 'Address';
    }

    public function getAttributeValueClassName()
    {
        return 'Concrete\Core\Entity\Attribute\Value\Value\AddressValue';
    }

    public function baseAttributeValues()
    {
        return [
            [
                $this->getAddress1(),
                $this->getAddress1(),
            ],
            [
                $this->getAddress1(false),
                $this->getAddress1(),
            ],
            [
                $this->getAddress2(),
                $this->getAddress2(),
            ],
            [
                $this->getAddress2(false),
                $this->getAddress2(),
            ],
        ];
    }

    public function displayAttributeValues()
    {
        return [
            [
                $this->getAddress1(),
                (
                    '<div class="ccm-address-text">' . "\n" .
                    '<span class="address-line1">123 Fake St.</span>' .
                    '<br>' . "\n" .
                    '<span class="address-line2">Suite 100</span>' .
                    '<br>' . "\n" .
                    '<span class="locality">Portland</span>, ' .
                    '<span class="administrative-area">Oregon</span> ' .
                    '<span class="postal-code">90000</span>' .
                    '<br>' . "\n" .
                    '<span class="country">United States</span>' .
                    "\n" . '</div>'
                ),
            ],
        ];
    }

    public function plaintextAttributeValues()
    {
        return [
            [
                $this->getAddress1(),
                "123 Fake St.\nSuite 100\nPortland, Oregon 90000\nUnited States",
            ],
        ];
    }

    public function searchIndexAttributeValues()
    {
        return [
            [
                $this->getAddress2(),
                [
                    'address1' => '500 SW Test',
                    'address2' => 'Suite 1',
                    'city' => 'Toronto',
                    'state_province' => 'ON',
                    'country' => 'CA',
                    'postal_code' => 'M4V 1W6',
                ],
            ],
        ];
    }

    /**
     * Tests that the the attribute value is formatted correctly when fetched
     * through the controller.
     */
    public function testControllerDisplayValue()
    {
        $expected = $this->displayAttributeValues();
        $ctrl = $this->getControllerWithValue();

        $this->assertEquals($expected[0][1], $ctrl->getDisplayValue());
    }

    /**
     * Tests address validation through the controller.
     */
    public function testControllerFormValidation()
    {
        $ctrl = $this->getControllerWithValue();

        // No country
        $this->assertFalse($ctrl->validateForm([
            'address1' => 'No country road 1',
            'state_province' => 'Unexisting',
            'city' => 'Atlantis',
            'postal_code' => '123456',
        ]));

        // US: default
        $this->assertTrue($ctrl->validateForm([
            'address1' => '123 Fake St.',
            'city' => 'Portland',
            'state_province' => 'OR',
            'country' => 'US',
            'postal_code' => '90000',
        ]));
        // US: state/province missing
        $this->assertFalse($ctrl->validateForm([
            'address1' => '123 Fake St.',
            'city' => 'Portland',
            'country' => 'US',
            'postal_code' => '90000',
        ]));

        // FI: default
        $this->assertTrue($ctrl->validateForm([
            'address1' => 'Olematon kuja 1',
            'city' => 'Helsinki',
            'country' => 'FI',
            'postal_code' => '00001',
        ]));
        // FI: city missing
        $this->assertFalse($ctrl->validateForm([
            'address1' => 'Olematon kuja 1',
            'country' => 'FI',
            'postal_code' => '00001',
        ]));
    }

    protected function getControllerWithValue()
    {
        $this->object->setAttribute(
            $this->getAttributeKeyHandle(),
            $this->getAddress1(false)
        );
        $val = $this->object->getAttribute($this->getAttributeKeyHandle());
        $gv = $val->getGenericValue();
        $key = $gv->getAttributeKey();
        $ctrl = $key->getController();
        $ctrl->setAttributeValue($val);

        return $ctrl;
    }

    protected function prepareBaseValueAfterRetrieving($value)
    {
        $value->setGenericValue(null);

        return $value;
    }

    protected function getAddress1($asObject = true)
    {
        if ($asObject) {
            $object = new \Concrete\Core\Entity\Attribute\Value\Value\AddressValue();
            $object->setAddress1('123 Fake St.');
            $object->setAddress2('Suite 100');
            $object->setCity('Portland');
            $object->setStateProvince('OR');
            $object->setCountry('US');
            $object->setPostalCode('90000');

            return $object;
        } else {
            return [
                'address1' => '123 Fake St.',
                'address2' => 'Suite 100',
                'city' => 'Portland',
                'state_province' => 'OR',
                'country' => 'US',
                'postal_code' => '90000',
            ];
        }
    }

    protected function getAddress2($asObject = true)
    {
        if ($asObject) {
            $object = new \Concrete\Core\Entity\Attribute\Value\Value\AddressValue();
            $object->setAddress1('500 SW Test');
            $object->setAddress2('Suite 1');
            $object->setCity('Toronto');
            $object->setStateProvince('ON');
            $object->setCountry('CA');
            $object->setPostalCode('M4V 1W6');

            return $object;
        } else {
            return [
                'address1' => '500 SW Test',
                'address2' => 'Suite 1',
                'city' => 'Toronto',
                'state_province' => 'ON',
                'country' => 'CA',
                'postal_code' => 'M4V 1W6',
            ];
        }
    }
}
