<?php

namespace Concrete\Tests\Form\Service;

use Concrete\Core\Http\Request;
use Core;
use PHPUnit_Framework_TestCase;

class FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\Form\Service\Form
     */
    protected static $formHelper;

    /**
     * @var \Concrete\Core\Http\Request
     */
    protected static $request;

    public static function setUpBeforeClass()
    {
        self::$request = new Request();
        self::$formHelper = Core::make('helper/form', ['request' => self::$request]);
        self::$formHelper->setRequest(self::$request);
    }

    public function providerTestCreateElements()
    {
        return [
            // submit
            [
                'submit',
                ['Key', 'Value'],
                '<input type="submit" class="btn ccm-input-submit" id="Key" name="Key" value="Value" />',
            ],
            [
                'submit',
                ['Key[]', 'Value'],
                '<input type="submit" class="btn ccm-input-submit" id="Key[]" name="Key[]" value="Value" />',
            ],
            [
                'submit',
                ['Key', 'Value', ['class' => 'MY-CLASS']],
                '<input type="submit" class="MY-CLASS btn ccm-input-submit" id="Key" name="Key" value="Value" />',
            ],
            [
                'submit',
                ['Key', 'Value', [], 'MY-CLASS'],
                '<input type="submit" class="btn ccm-input-submit MY-CLASS" id="Key" name="Key" value="Value" />',
            ],
            // button
            [
                'button',
                ['Key', 'Value'],
                '<input type="button" class="btn ccm-input-button" id="Key" name="Key" value="Value" />',
            ],
            [
                'button',
                ['Key[]', 'Value'],
                '<input type="button" class="btn ccm-input-button" id="Key[]" name="Key[]" value="Value" />',
            ],
            [
                'button',
                ['Key', 'Value', ['class' => 'MY-CLASS']],
                '<input type="button" class="MY-CLASS btn ccm-input-button" id="Key" name="Key" value="Value" />',
            ],
            [
                'button',
                ['Key', 'Value', [], 'MY-CLASS'],
                '<input type="button" class="btn ccm-input-button MY-CLASS" id="Key" name="Key" value="Value" />',
            ],
            // label
            [
                'label',
                ['ForKey', '<b>label</b>'],
                '<label for="ForKey" class="control-label"><b>label</b></label>',
            ],
            [
                'label',
                ['ForKey[]', 'text'],
                '<label for="ForKey[]" class="control-label">text</label>',
            ],
            [
                'label',
                ['ForKey', 'text', []],
                '<label for="ForKey" class="control-label">text</label>',
            ],
            [
                'label',
                ['ForKey', 'text', ['class' => 'MY-CLASS']],
                '<label for="ForKey" class="MY-CLASS control-label">text</label>',
            ],
            // file
            [
                'file',
                ['Key'],
                '<input type="file" id="Key" name="Key" value="" class="form-control" />',
            ],
            [
                'file',
                ['Key[]'],
                '<input type="file" id="Key[]" name="Key[]" value="" class="form-control" />',
            ],
            [
                'file',
                ['Key', []],
                '<input type="file" id="Key" name="Key" value="" class="form-control" />',
            ],
            [
                'file',
                ['Key', ['class' => 'MY-CLASS']],
                '<input type="file" id="Key" name="Key" value="" class="MY-CLASS form-control" />',
            ],
            // hidden
            [
                'hidden',
                ['Key'],
                '<input type="hidden" id="Key" name="Key" value="" />',
            ],
            [
                'hidden',
                ['Key', null],
                '<input type="hidden" id="Key" name="Key" value="" />',
            ],
            [
                'hidden',
                ['Key', null, ['class' => 'MY-CLASS']],
                '<input type="hidden" id="Key" name="Key" class="MY-CLASS" value="" />',
            ],
            [
                'hidden',
                ['Key', ''],
                '<input type="hidden" id="Key" name="Key" value="" />',
            ],
            [
                'hidden',
                ['Key', '', ['class' => 'MY-CLASS']],
                '<input type="hidden" id="Key" name="Key" class="MY-CLASS" value="" />',
            ],
            [
                'hidden',
                ['Key', 'Field value'],
                '<input type="hidden" id="Key" name="Key" value="Field value" />',
            ],
            [
                'hidden',
                ['Key', 'Field value', ['class' => 'MY-CLASS', 'data-my-data' => 'My value']],
                '<input type="hidden" id="Key" name="Key" class="MY-CLASS" data-my-data="My value" value="Field value" />',
            ],
            [
                'hidden',
                ['Key', false],
                '<input type="hidden" id="Key" name="Key" value="" />',
            ],
            [
                'hidden',
                ['Key', 1],
                '<input type="hidden" id="Key" name="Key" value="1" />',
            ],
            [
                'hidden',
                ['Key', 'Original value'],
                '<input type="hidden" id="Key" name="Key" value="Original value" />',
            ],
            [
                'hidden',
                ['Key', 'Original value'],
                '<input type="hidden" id="Key" name="Key" value="" />',
                ['Key' => ''],
            ],
            [
                'hidden',
                ['Key[]'],
                '<input type="hidden" id="Key[]" name="Key[]" value="" />',
            ],
            [
                'hidden',
                ['Key', 'Original value'],
                '<input type="hidden" id="Key" name="Key" value="Received value" />',
                ['Key' => 'Received value'],
            ],
            [
                'hidden',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="hidden" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" />',
                ['Key' => 'Received value'],
            ],
            [
                'hidden',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="hidden" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" />',
                ['Key' => ['subkey1' => 'Received value']],
            ],
            [
                'hidden',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="hidden" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Received value" />',
                ['Key' => ['subkey1' => ['subkey2' => 'Received value']]],
            ],
            [
                'hidden',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="hidden" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" />',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => 'Received value']]]],
            ],
            // checkbox
            [
                'checkbox',
                ['Key', 'Value'],
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" />',
            ],
            [
                'checkbox',
                ['Key', 'Value', false],
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" />',
            ],
            [
                'checkbox',
                ['Key', 'Value', null],
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" />',
            ],
            [
                'checkbox',
                ['Key', 'Value', 0],
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" />',
            ],
            [
                'checkbox',
                ['Key', 'Value', '0'],
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" />',
            ],
            [
                'checkbox',
                ['Key', 'Value', true],
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" checked="checked" />',
            ],
            [
                'checkbox',
                ['Key', 'Value', '1'],
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" checked="checked" />',
            ],
            [
                'checkbox',
                ['Key', 'Value', true],
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" />',
                ['OtherField' => 'Other value'],
            ],
            [
                'checkbox',
                ['Key', 'Value', false],
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" checked="checked" />',
                ['Key' => 'Value'],
            ],
            [
                'checkbox',
                ['Key', 'Value', false],
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" />',
                ['Key' => ''],
            ],
            [
                'checkbox',
                ['Key', 'Value', false],
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" />',
                ['Key' => 'Wrong value'],
            ],
            [
                'checkbox',
                ['Key[]', 'Value'],
                '<input type="checkbox" id="Key_Value" name="Key[]" class="ccm-input-checkbox" value="Value" />',
            ],
            [
                'checkbox',
                ['Key[]', 'Value', true],
                '<input type="checkbox" id="Key_Value" name="Key[]" class="ccm-input-checkbox" value="Value" checked="checked" />',
            ],
            [
                'checkbox',
                ['Key[]', 'Value', true],
                '<input type="checkbox" id="Key_Value" name="Key[]" class="ccm-input-checkbox" value="Value" />',
                ['OtherField' => 'Other value'],
            ],
            [
                'checkbox',
                ['Key[]', 'Value', false],
                '<input type="checkbox" id="Key_Value" name="Key[]" class="ccm-input-checkbox" value="Value" checked="checked" />',
                ['Key' => 'Value'],
            ],
            [
                'checkbox',
                ['Key[]', 'Value', false],
                '<input type="checkbox" id="Key_Value" name="Key[]" class="ccm-input-checkbox" value="Value" />',
                ['Key' => 'Other value'],
            ],
            [
                'checkbox',
                ['Key[]', 'Value', false],
                '<input type="checkbox" id="Key_Value" name="Key[]" class="ccm-input-checkbox" value="Value" checked="checked" />',
                ['Key' => ['Look', 'for', 'Value', 'in', 'this', 'array']],
            ],
            [
                'checkbox',
                ['Key[subkey1][subkey2]', 'Value', false],
                '<input type="checkbox" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="ccm-input-checkbox" value="Value" />',
                ['Key' => 'Value'],
            ],
            [
                'checkbox',
                ['Key[subkey1][subkey2]', 'Value', false],
                '<input type="checkbox" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="ccm-input-checkbox" value="Value" />',
                ['Key' => ['subkey1' => 'Value']],
            ],
            [
                'checkbox',
                ['Key[subkey1][subkey2]', 'Value', false],
                '<input type="checkbox" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="ccm-input-checkbox" value="Value" checked="checked" />',
                ['Key' => ['subkey1' => ['subkey2' => 'Value']]],
            ],
            [
                'checkbox',
                ['Key[subkey1][subkey2]', 'Value', false],
                '<input type="checkbox" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="ccm-input-checkbox" value="Value" checked="checked" />',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => 'Value']]]],
            ],
            [
                'checkbox',
                ['Key[subkey1][subkey2]', 'Value', false],
                '<input type="checkbox" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="ccm-input-checkbox" value="Value" />',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => 'Other value']]]],
            ],
            // textarea
            [
                'textarea',
                ['Key'],
                '<textarea id="Key" name="Key" class="form-control"></textarea>',
            ],
            [
                'textarea',
                ['Key', 'Value'],
                '<textarea id="Key" name="Key" class="form-control">Value</textarea>',
            ],
            [
                'textarea',
                ['Key', ['class' => 'MY-CLASS']],
                '<textarea id="Key" name="Key" class="MY-CLASS form-control"></textarea>',
            ],
            [
                'textarea',
                ['Key', '', ['class' => 'MY-CLASS']],
                '<textarea id="Key" name="Key" class="MY-CLASS form-control"></textarea>',
            ],
            [
                'textarea',
                ['Key', 'Value', ['class' => 'MY-CLASS']],
                '<textarea id="Key" name="Key" class="MY-CLASS form-control">Value</textarea>',
            ],
            [
                'textarea',
                ['Key', 'Original value'],
                '<textarea id="Key" name="Key" class="form-control"></textarea>',
                ['Key' => ''],
            ],
            [
                'textarea',
                ['Key', 'Original value'],
                '<textarea id="Key" name="Key" class="form-control">Received value</textarea>',
                ['Key' => 'Received value'],
            ],
            [
                'textarea',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<textarea id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="form-control">Original value</textarea>',
                ['Key' => 'Received value'],
            ],
            [
                'textarea',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<textarea id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="form-control">Original value</textarea>',
                ['Key' => ['subkey1' => 'Received value']],
            ],
            [
                'textarea',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<textarea id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="form-control">Received value</textarea>',
                ['Key' => ['subkey1' => ['subkey2' => 'Received value']]],
            ],
            [
                'textarea',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<textarea id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="form-control">Original value</textarea>',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => 'Received value']]]],
            ],
            // radio
            [
                'radio',
                ['Key', 'Value'],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="ccm-input-radio" />',
            ],
            [
                'radio',
                ['Key', 'Value', 'Incorrect value'],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="ccm-input-radio" />',
            ],
            [
                'radio',
                ['Key', 'Value', 'Value'],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="ccm-input-radio" checked="checked" />',
            ],
            [
                'radio',
                ['Key', 'Value', ['class' => 'MY-CLASS', 'data-custom' => 'My custom data']],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="MY-CLASS ccm-input-radio" data-custom="My custom data" />',
            ],
            [
                'radio',
                ['Key', 'Value', 'Value', ['class' => 'MY-CLASS']],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="MY-CLASS ccm-input-radio" checked="checked" />',
            ],
            [
                'radio',
                ['Key', 'Value'],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="ccm-input-radio" />',
                ['Key' => ''],
            ],
            [
                'radio',
                ['Key', 'Value'],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="ccm-input-radio" />',
                ['Key' => 'Invalid value'],
            ],
            [
                'radio',
                ['Key', 'Value'],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="ccm-input-radio" checked="checked" />',
                ['Key' => 'Value'],
            ],
            [
                'radio',
                ['Key', 'Value'],
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="ccm-input-radio" />',
                ['OtherKey' => 'OtherValue'],
            ],
            [
                'radio',
                ['Key[subkey1][subkey2]', 'Value'],
                '<input type="radio" id="Key[subkey1][subkey2]**UNIQUENUMBER**" name="Key[subkey1][subkey2]" value="Value" class="ccm-input-radio" checked="checked" />',
                ['Key' => ['subkey1' => ['subkey2' => 'Value']]],
            ],
            // inputType (text, number, email, telephone, url, search, password)
            [
                'text',
                ['Key'],
                '<input type="text" id="Key" name="Key" value="" class="form-control ccm-input-text" />',
            ],
            [
                'number',
                ['Key'],
                '<input type="number" id="Key" name="Key" value="" class="form-control ccm-input-number" />',
            ],
            [
                'email',
                ['Key'],
                '<input type="email" id="Key" name="Key" value="" class="form-control ccm-input-email" />',
            ],
            [
                'telephone',
                ['Key'],
                '<input type="tel" id="Key" name="Key" value="" class="form-control ccm-input-tel" />',
            ],
            [
                'url',
                ['Key'],
                '<input type="url" id="Key" name="Key" value="" class="form-control ccm-input-url" />',
            ],
            [
                'search',
                ['Key'],
                '<input type="search" id="Key" name="Key" value="" class="form-control ccm-input-search" />',
            ],
            [
                'password',
                ['Key'],
                '<input type="password" id="Key" name="Key" value="" class="form-control ccm-input-password" />',
            ],
            [
                'text',
                ['Key', null],
                '<input type="text" id="Key" name="Key" value="" class="form-control ccm-input-text" />',
            ],
            [
                'text',
                ['Key', false],
                '<input type="text" id="Key" name="Key" value="" class="form-control ccm-input-text" />',
            ],
            [
                'text',
                ['Key', 'Value'],
                '<input type="text" id="Key" name="Key" value="Value" class="form-control ccm-input-text" />',
            ],
            [
                'text',
                ['Key', ['data-mine' => 'MY DATA', 'class' => 'MY-CLASS']],
                '<input type="text" id="Key" name="Key" value="" data-mine="MY DATA" class="MY-CLASS form-control ccm-input-text" />',
            ],
            [
                'text',
                ['Key', 'Value', ['class' => 'MY-CLASS']],
                '<input type="text" id="Key" name="Key" value="Value" class="MY-CLASS form-control ccm-input-text" />',
            ],
            [
                'text',
                ['Key', 'Value'],
                '<input type="text" id="Key" name="Key" value="Received value" class="form-control ccm-input-text" />',
                ['Key' => 'Received value'],
            ],
            [
                'text',
                ['Key', 'Value'],
                '<input type="text" id="Key" name="Key" value="Value" class="form-control ccm-input-text" />',
                ['OtherKey' => 'Other value'],
            ],
            [
                'text',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" class="form-control ccm-input-text" />',
            ],
            [
                'text',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" class="form-control ccm-input-text" />',
                ['OtherKey' => 'Other value'],
            ],
            [
                'text',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" class="form-control ccm-input-text" />',
                ['Key' => 'Received value'],
            ],
            [
                'text',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" class="form-control ccm-input-text" />',
                ['Key' => ['subkey1' => 'Received value']],
            ],
            [
                'text',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Received value" class="form-control ccm-input-text" />',
                ['Key' => ['subkey1' => ['subkey2' => 'Received value']]],
            ],
            [
                'text',
                ['Key[subkey1][subkey2]', 'Original value'],
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" class="form-control ccm-input-text" />',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => 'Received value']]]],
            ],
            // select
            [
                'select',
                ['Key', null],
                '<select id="Key" name="Key" class="form-control"></select>',
            ],
            [
                'select',
                ['Key', false],
                '<select id="Key" name="Key" class="form-control"></select>',
            ],
            [
                'select',
                ['Key', ''],
                '<select id="Key" name="Key" class="form-control"></select>',
            ],
            [
                'select',
                ['Key', []],
                '<select id="Key" name="Key" class="form-control"></select>',
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second']],
                '<select id="Key" name="Key" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key" name="Key" ccm-passed-value="Two" class="form-control"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second'], ['class' => 'MY-CLASS', 'data-mine' => 'MY DATA']],
                '<select id="Key" name="Key" class="MY-CLASS form-control" data-mine="MY DATA"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two', ['class' => 'MY-CLASS', 'data-mine' => 'MY DATA']],
                '<select id="Key" name="Key" class="MY-CLASS form-control" data-mine="MY DATA" ccm-passed-value="Two"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Invalid'],
                '<select id="Key" name="Key" ccm-passed-value="Invalid" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key" name="Key" ccm-passed-value="Two" class="form-control"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
                ['OtherField' => 'OtherValue'],
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key" name="Key" ccm-passed-value="Two" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
                ['Key' => ''],
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key" name="Key" ccm-passed-value="Two" class="form-control"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['Key' => 'One'],
            ],
            [
                'select',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key" name="Key" ccm-passed-value="Two" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
                ['Key' => 'Invalid'],
            ],
            [
                'select',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" ccm-passed-value="Two" class="form-control"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
                ['Key' => 'One'],
            ],
            [
                'select',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" ccm-passed-value="Two" class="form-control"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
                ['Key' => ['subkey1' => 'One']],
            ],
            [
                'select',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" ccm-passed-value="Two" class="form-control"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['Key' => ['subkey1' => ['subkey2' => 'One']]],
            ],
            [
                'select',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" ccm-passed-value="Two" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => 'One']]]],
            ],
            // selectMultiple
            [
                'selectMultiple',
                ['Key', null],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"></select>',
            ],
            [
                'selectMultiple',
                ['Key', []],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second']],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], false],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], null],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], []],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], false, ['data-mine' => 'MY DATA', 'class' => 'MY-CLASS']],
                '<select id="Key" name="Key[]" multiple="multiple" data-mine="MY DATA" class="MY-CLASS form-control"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], ['Two']],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], ['Invalid']],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], ['Two', 'One']],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One" selected="selected">First</option><option value="Two" selected="selected">Second</option></select>',
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second']],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['Key' => 'One'],
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second']],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
                ['Key' => []],
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second']],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['Key' => ['One']],
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['Key' => ['One']],
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second']],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One" selected="selected">First</option><option value="Two" selected="selected">Second</option></select>',
                ['Key' => ['One', 'Two']],
            ],
            [
                'selectMultiple',
                ['Key', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One" selected="selected">First</option><option value="Two" selected="selected">Second</option></select>',
                ['Key' => ['One', 'Two']],
            ],
            [
                'selectMultiple',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2][]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
                ['Key' => ['One']],
            ],
            [
                'selectMultiple',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2][]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
                ['Key' => ['subkey1' => ['One']]],
            ],
            [
                'selectMultiple',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2][]" multiple="multiple" class="form-control"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['Key' => ['subkey1' => ['subkey2' => ['One']]]],
            ],
            [
                'selectMultiple',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2][]" multiple="multiple" class="form-control"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => 'One']]]],
            ],
            [
                'selectMultiple',
                ['Key[subkey1][subkey2]', ['One' => 'First', 'Two' => 'Second'], 'Two'],
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2][]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
                ['Key' => ['subkey1' => ['subkey2' => ['subkey3' => ['One']]]]],
            ],
        ];
    }

    /**
     * @dataProvider providerTestCreateElements
     *
     * @param mixed $method
     * @param array $args
     * @param mixed $expected
     * @param array $post
     */
    public function testCreateElements($method, array $args, $expected, array $post = [])
    {
        if (empty($post)) {
            self::$request->initialize();
        } else {
            self::$request->initialize([], $post, [], [], [], ['REQUEST_METHOD' => 'POST']);
        }
        $calculated = call_user_func_array([static::$formHelper, $method], $args);
        if (strpos($expected, '**UNIQUENUMBER**') === false) {
            $this->assertSame($expected, $calculated);
        } else {
            $chunks = explode('**UNIQUENUMBER**', $expected);
            array_walk($chunks, function (&$chunk, $index) {
                $chunk = preg_quote($chunk, '/');
            });
            $rx = '/^' . implode('\d+', $chunks) . '$/';
            $this->assertRegExp($rx, $calculated);
        }
    }
}
