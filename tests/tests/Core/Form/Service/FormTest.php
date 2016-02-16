<?php
namespace Concrete\Tests\Core\Form\Service;

use Core;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\Form\Service\Form
     */
    protected static $formHelper;

    /**
     * @var array
     */
    protected $initialState;

    public static function setUpBeforeClass()
    {
        self::$formHelper = Core::make('helper/form');
    }
    protected function setUp()
    {
        $this->initialState = array(
            'get' => $_GET,
            'post' => $_POST,
            'request' => $_REQUEST,
            'requestMethod' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null,
        );
        $_GET = array();
        $_POST = array();
        $_REQUEST = array();
        unset($_SERVER['REQUEST_METHOD']);
    }
    protected function tearDown()
    {
        $_GET = $this->initialState['get'];
        $_POST = $this->initialState['post'];
        $_REQUEST = $this->initialState['request'];
        unset($_SERVER['REQUEST_METHOD']);
        if (isset($this->initialState['REQUEST_METHOD'])) {
            $_SERVER['REQUEST_METHOD'] = $this->initialState['REQUEST_METHOD'];
        }
    }

    public function providerTestCreateElements()
    {
        return array(
            // submit
            array(
                'submit',
                array('Key', 'Value'),
                '<input type="submit" class="btn ccm-input-submit" id="Key" name="Key" value="Value" />',
            ),
            array(
                'submit',
                array('Key[]', 'Value'),
                '<input type="submit" class="btn ccm-input-submit" id="Key[]" name="Key[]" value="Value" />',

            ),
            array(
                'submit',
                array('Key', 'Value', array('class' => 'MY-CLASS')),
                '<input type="submit" class="MY-CLASS btn ccm-input-submit" id="Key" name="Key" value="Value" />',
            ),
            array(
                'submit',
                array('Key', 'Value', array(), 'MY-CLASS'),
                '<input type="submit" class="btn ccm-input-submit MY-CLASS" id="Key" name="Key" value="Value" />',
            ),
            // button
            array(
                'button',
                array('Key', 'Value'),
                '<input type="button" class="btn ccm-input-button" id="Key" name="Key" value="Value" />',
            ),
            array(
                'button',
                array('Key[]', 'Value'),
                '<input type="button" class="btn ccm-input-button" id="Key[]" name="Key[]" value="Value" />',
            ),
            array(
                'button',
                array('Key', 'Value', array('class' => 'MY-CLASS')),
                '<input type="button" class="MY-CLASS btn ccm-input-button" id="Key" name="Key" value="Value" />',
            ),
            array(
                'button',
                array('Key', 'Value', array(), 'MY-CLASS'),
                '<input type="button" class="btn ccm-input-button MY-CLASS" id="Key" name="Key" value="Value" />',
            ),
            // label
            array(
                'label',
                array('ForKey', '<b>label</b>'),
                '<label for="ForKey" class="control-label"><b>label</b></label>',
            ),
            array(
                'label',
                array('ForKey[]', 'text'),
                '<label for="ForKey[]" class="control-label">text</label>',
            ),
            array(
                'label',
                array('ForKey', 'text', array()),
                '<label for="ForKey" class="control-label">text</label>',
            ),
            array(
                'label',
                array('ForKey', 'text', array('class' => 'MY-CLASS')),
                '<label for="ForKey" class="MY-CLASS control-label">text</label>',
            ),
            // file
            array(
                'file',
                array('Key'),
                '<input type="file" id="Key" name="Key" value="" class="form-control" />',
            ),
            array(
                'file',
                array('Key[]'),
                '<input type="file" id="Key[]" name="Key[]" value="" class="form-control" />',
            ),
            array(
                'file',
                array('Key', array()),
                '<input type="file" id="Key" name="Key" value="" class="form-control" />',
            ),
            array(
                'file',
                array('Key', array('class' => 'MY-CLASS')),
                '<input type="file" id="Key" name="Key" value="" class="MY-CLASS form-control" />',
            ),
            // hidden
            array(
                'hidden',
                array('Key'),
                '<input type="hidden" id="Key" name="Key" value="" />',
            ),
            array(
                'hidden',
                array('Key', null),
                '<input type="hidden" id="Key" name="Key" value="" />',
            ),
            array(
                'hidden',
                array('Key', null, array('class' => 'MY-CLASS')),
                '<input type="hidden" id="Key" name="Key" class="MY-CLASS" value="" />',
            ),
            array(
                'hidden',
                array('Key', ''),
                '<input type="hidden" id="Key" name="Key" value="" />',
            ),
            array(
                'hidden',
                array('Key', '', array('class' => 'MY-CLASS')),
                '<input type="hidden" id="Key" name="Key" class="MY-CLASS" value="" />',
            ),
            array(
                'hidden',
                array('Key', 'Field value'),
                '<input type="hidden" id="Key" name="Key" value="Field value" />',
            ),
            array(
                'hidden',
                array('Key', 'Field value', array('class' => 'MY-CLASS', 'data-my-data' => 'My value')),
                '<input type="hidden" id="Key" name="Key" class="MY-CLASS" data-my-data="My value" value="Field value" />',
            ),
            array(
                'hidden',
                array('Key', false),
                '<input type="hidden" id="Key" name="Key" value="" />',
            ),
            array(
                'hidden',
                array('Key', 1),
                '<input type="hidden" id="Key" name="Key" value="1" />',
            ),
            array(
                'hidden',
                array('Key', 'Original value'),
                '<input type="hidden" id="Key" name="Key" value="Original value" />',
            ),
            array(
                'hidden',
                array('Key', 'Original value'),
                '<input type="hidden" id="Key" name="Key" value="" />',
                array('Key' => ''),
            ),
            array(
                'hidden',
                array('Key[]'),
                '<input type="hidden" id="Key[]" name="Key[]" value="" />',
            ),
            array(
                'hidden',
                array('Key', 'Original value'),
                '<input type="hidden" id="Key" name="Key" value="Received value" />',
                array('Key' => 'Received value'),
            ),
            array(
                'hidden',
                array('Key[subkey1][subkey2]', 'Original value'),
                '<input type="hidden" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" />',
                array('Key' => 'Received value'),
            ),
            array(
                'hidden',
                array('Key[subkey1][subkey2]', 'Original value'),
                '<input type="hidden" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" />',
                array('Key' => array('subkey1' => 'Received value')),
            ),
            array(
                'hidden',
                array('Key[subkey1][subkey2]', 'Original value'),
                '<input type="hidden" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Received value" />',
                array('Key' => array('subkey1' => array('subkey2' => 'Received value'))),
            ),
            array(
                'hidden',
                array('Key[subkey1][subkey2]', 'Original value'),
                '<input type="hidden" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" />',
                array('Key' => array('subkey1' => array('subkey2' => array('subkey3' => 'Received value')))),
            ),
            // checkbox
            array(
                'checkbox',
                array('Key', 'Value'),
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" />',
            ),
            array(
                'checkbox',
                array('Key', 'Value', false),
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" />',
            ),
            array(
                'checkbox',
                array('Key', 'Value', null),
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" />',
            ),
            array(
                'checkbox',
                array('Key', 'Value', 0),
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" />',
            ),
            array(
                'checkbox',
                array('Key', 'Value', '0'),
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" />',
            ),
            array(
                'checkbox',
                array('Key', 'Value', true),
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" checked="checked" />',
            ),
            array(
                'checkbox',
                array('Key', 'Value', '1'),
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" checked="checked" />',
            ),
            array(
                'checkbox',
                array('Key', 'Value', true),
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" />',
                array('OtherField' => 'Other value'),
            ),
            array(
                'checkbox',
                array('Key', 'Value', false),
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" checked="checked" />',
                array('Key' => 'Value'),
            ),
            array(
                'checkbox',
                array('Key', 'Value', false),
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" />',
                array('Key' => ''),
            ),
            array(
                'checkbox',
                array('Key', 'Value', false),
                '<input type="checkbox" id="Key" name="Key" class="ccm-input-checkbox" value="Value" />',
                array('Key' => 'Wrong value'),
            ),
            array(
                'checkbox',
                array('Key[]', 'Value'),
                '<input type="checkbox" id="Key_Value" name="Key[]" class="ccm-input-checkbox" value="Value" />',
            ),
            array(
                'checkbox',
                array('Key[]', 'Value', true),
                '<input type="checkbox" id="Key_Value" name="Key[]" class="ccm-input-checkbox" value="Value" checked="checked" />',
            ),
            array(
                'checkbox',
                array('Key[]', 'Value', true),
                '<input type="checkbox" id="Key_Value" name="Key[]" class="ccm-input-checkbox" value="Value" />',
                array('OtherField' => 'Other value'),
            ),
            array(
                'checkbox',
                array('Key[]', 'Value', false),
                '<input type="checkbox" id="Key_Value" name="Key[]" class="ccm-input-checkbox" value="Value" checked="checked" />',
                array('Key' => 'Value'),
            ),
            array(
                'checkbox',
                array('Key[]', 'Value', false),
                '<input type="checkbox" id="Key_Value" name="Key[]" class="ccm-input-checkbox" value="Value" />',
                array('Key' => 'Other value'),
            ),
            array(
                'checkbox',
                array('Key[]', 'Value', false),
                '<input type="checkbox" id="Key_Value" name="Key[]" class="ccm-input-checkbox" value="Value" checked="checked" />',
                array('Key' => array('Look', 'for', 'Value', 'in', 'this', 'array')),
            ),
            array(
                'checkbox',
                array('Key[subkey1][subkey2]', 'Value', false),
                '<input type="checkbox" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="ccm-input-checkbox" value="Value" />',
                array('Key' => 'Value'),
            ),
            array(
                'checkbox',
                array('Key[subkey1][subkey2]', 'Value', false),
                '<input type="checkbox" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="ccm-input-checkbox" value="Value" />',
                array('Key' => array('subkey1' => 'Value')),
            ),
            array(
                'checkbox',
                array('Key[subkey1][subkey2]', 'Value', false),
                '<input type="checkbox" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="ccm-input-checkbox" value="Value" checked="checked" />',
                array('Key' => array('subkey1' => array('subkey2' => 'Value'))),
            ),
            array(
                'checkbox',
                array('Key[subkey1][subkey2]', 'Value', false),
                '<input type="checkbox" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="ccm-input-checkbox" value="Value" checked="checked" />',
                array('Key' => array('subkey1' => array('subkey2' => array('subkey3' => 'Value')))),
            ),
            array(
                'checkbox',
                array('Key[subkey1][subkey2]', 'Value', false),
                '<input type="checkbox" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="ccm-input-checkbox" value="Value" />',
                array('Key' => array('subkey1' => array('subkey2' => array('subkey3' => 'Other value')))),
            ),
            // textarea
            array(
                'textarea',
                array('Key'),
                '<textarea id="Key" name="Key" class="form-control"></textarea>',
            ),
            array(
                'textarea',
                array('Key', 'Value'),
                '<textarea id="Key" name="Key" class="form-control">Value</textarea>',
            ),
            array(
                'textarea',
                array('Key', array('class' => 'MY-CLASS')),
                '<textarea id="Key" name="Key" class="MY-CLASS form-control"></textarea>',
            ),
            array(
                'textarea',
                array('Key', '', array('class' => 'MY-CLASS')),
                '<textarea id="Key" name="Key" class="MY-CLASS form-control"></textarea>',
            ),
            array(
                'textarea',
                array('Key', 'Value', array('class' => 'MY-CLASS')),
                '<textarea id="Key" name="Key" class="MY-CLASS form-control">Value</textarea>',
            ),
            array(
                'textarea',
                array('Key', 'Original value'),
                '<textarea id="Key" name="Key" class="form-control"></textarea>',
                array('Key' => ''),
            ),
            array(
                'textarea',
                array('Key', 'Original value'),
                '<textarea id="Key" name="Key" class="form-control">Received value</textarea>',
                array('Key' => 'Received value'),
            ),
            array(
                'textarea',
                array('Key[subkey1][subkey2]', 'Original value'),
                '<textarea id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="form-control">Original value</textarea>',
                array('Key' => 'Received value'),
            ),
            array(
                'textarea',
                array('Key[subkey1][subkey2]', 'Original value'),
                '<textarea id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="form-control">Original value</textarea>',
                array('Key' => array('subkey1' => 'Received value')),
            ),
            array(
                'textarea',
                array('Key[subkey1][subkey2]', 'Original value'),
                '<textarea id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="form-control">Received value</textarea>',
                array('Key' => array('subkey1' => array('subkey2' => 'Received value'))),
            ),
            array(
                'textarea',
                array('Key[subkey1][subkey2]', 'Original value'),
                '<textarea id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" class="form-control">Original value</textarea>',
                array('Key' => array('subkey1' => array('subkey2' => array('subkey3' => 'Received value')))),
            ),
            // radio
            array(
                'radio',
                array('Key', 'Value'),
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="ccm-input-radio" />',
            ),
            array(
                'radio',
                array('Key', 'Value', 'Incorrect value'),
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="ccm-input-radio" />',
            ),
            array(
                'radio',
                array('Key', 'Value', 'Value'),
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="ccm-input-radio" checked="checked" />',
            ),
            array(
                'radio',
                array('Key', 'Value', array('class' => 'MY-CLASS', 'data-custom' => 'My custom data')),
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="MY-CLASS ccm-input-radio" data-custom="My custom data" />',
            ),
            array(
                'radio',
                array('Key', 'Value', 'Value', array('class' => 'MY-CLASS')),
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="MY-CLASS ccm-input-radio" checked="checked" />',
            ),
            array(
                'radio',
                array('Key', 'Value'),
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="ccm-input-radio" />',
                array('Key' => ''),
            ),
            array(
                'radio',
                array('Key', 'Value'),
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="ccm-input-radio" />',
                array('Key' => 'Invalid value'),
            ),
            array(
                'radio',
                array('Key', 'Value'),
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="ccm-input-radio" checked="checked" />',
                array('Key' => 'Value'),
            ),
            array(
                'radio',
                array('Key', 'Value'),
                '<input type="radio" id="Key**UNIQUENUMBER**" name="Key" value="Value" class="ccm-input-radio" />',
                array('OtherKey' => 'OtherValue'),
            ),
            array(
                'radio',
                array('Key[subkey1][subkey2]', 'Value'),
                '<input type="radio" id="Key[subkey1][subkey2]**UNIQUENUMBER**" name="Key[subkey1][subkey2]" value="Value" class="ccm-input-radio" />',
                array('Key' => array('subkey1' => array('subkey2' => 'Value'))),
            ),
            // inputType (text, number, email, telephone, url, search, password)
            array(
                'text',
                array('Key'),
                '<input type="text" id="Key" name="Key" value="" class="form-control ccm-input-text" />',
            ),
            array(
                'number',
                array('Key'),
                '<input type="number" id="Key" name="Key" value="" class="form-control ccm-input-number" />',
            ),
            array(
                'email',
                array('Key'),
                '<input type="email" id="Key" name="Key" value="" class="form-control ccm-input-email" />',
            ),
            array(
                'telephone',
                array('Key'),
                '<input type="tel" id="Key" name="Key" value="" class="form-control ccm-input-tel" />',
            ),
            array(
                'url',
                array('Key'),
                '<input type="url" id="Key" name="Key" value="" class="form-control ccm-input-url" />',
            ),
            array(
                'search',
                array('Key'),
                '<input type="search" id="Key" name="Key" value="" class="form-control ccm-input-search" />',
            ),
            array(
                'password',
                array('Key'),
                '<input type="password" id="Key" name="Key" value="" class="form-control ccm-input-password" />',
            ),
            array(
                'text',
                array('Key', null),
                '<input type="text" id="Key" name="Key" value="" class="form-control ccm-input-text" />',
            ),
            array(
                'text',
                array('Key', false),
                '<input type="text" id="Key" name="Key" value="" class="form-control ccm-input-text" />',
            ),
            array(
                'text',
                array('Key', 'Value'),
                '<input type="text" id="Key" name="Key" value="Value" class="form-control ccm-input-text" />',
            ),
            array(
                'text',
                array('Key', array('data-mine' => 'MY DATA', 'class' => 'MY-CLASS')),
                '<input type="text" id="Key" name="Key" value="" data-mine="MY DATA" class="MY-CLASS form-control ccm-input-text" />',
            ),
            array(
                'text',
                array('Key', 'Value', array('class' => 'MY-CLASS')),
                '<input type="text" id="Key" name="Key" value="Value" class="MY-CLASS form-control ccm-input-text" />',
            ),
            array(
                'text',
                array('Key', 'Value'),
                '<input type="text" id="Key" name="Key" value="Received value" class="form-control ccm-input-text" />',
                array('Key' => 'Received value'),
            ),
            array(
                'text',
                array('Key', 'Value'),
                '<input type="text" id="Key" name="Key" value="Value" class="form-control ccm-input-text" />',
                array('OtherKey' => 'Other value'),
            ),
            array(
                'text',
                array('Key[subkey1][subkey2]', 'Original value'),
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" class="form-control ccm-input-text" />',
            ),
            array(
                'text',
                array('Key[subkey1][subkey2]', 'Original value'),
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" class="form-control ccm-input-text" />',
                array('OtherKey' => 'Other value'),
            ),
            array(
                'text',
                array('Key[subkey1][subkey2]', 'Original value'),
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" class="form-control ccm-input-text" />',
                array('Key' => 'Received value'),
            ),
            array(
                'text',
                array('Key[subkey1][subkey2]', 'Original value'),
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" class="form-control ccm-input-text" />',
                array('Key' => array('subkey1' => 'Received value')),
            ),
            array(
                'text',
                array('Key[subkey1][subkey2]', 'Original value'),
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Received value" class="form-control ccm-input-text" />',
                array('Key' => array('subkey1' => array('subkey2' => 'Received value'))),
            ),
            array(
                'text',
                array('Key[subkey1][subkey2]', 'Original value'),
                '<input type="text" id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" value="Original value" class="form-control ccm-input-text" />',
                array('Key' => array('subkey1' => array('subkey2' => array('subkey3' => 'Received value')))),
            ),
            // select
            array(
                'select',
                array('Key', null),
                '<select id="Key" name="Key" class="form-control"></select>',
            ),
            array(
                'select',
                array('Key', false),
                '<select id="Key" name="Key" class="form-control"></select>',
            ),
            array(
                'select',
                array('Key', ''),
                '<select id="Key" name="Key" class="form-control"></select>',
            ),
            array(
                'select',
                array('Key', array()),
                '<select id="Key" name="Key" class="form-control"></select>',
            ),
            array(
                'select',
                array('Key', array('One' => 'First', 'Two' => 'Second')),
                '<select id="Key" name="Key" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
            ),
            array(
                'select',
                array('Key', array('One' => 'First', 'Two' => 'Second'), 'Two'),
                '<select id="Key" name="Key" ccm-passed-value="Two" class="form-control"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
            ),
            array(
                'select',
                array('Key', array('One' => 'First', 'Two' => 'Second'), array('class' => 'MY-CLASS', 'data-mine' => "MY DATA")),
                '<select id="Key" name="Key" class="MY-CLASS form-control" data-mine="MY DATA"><option value="One">First</option><option value="Two">Second</option></select>',
            ),
            array(
                'select',
                array('Key', array('One' => 'First', 'Two' => 'Second'), 'Two', array('class' => 'MY-CLASS', 'data-mine' => "MY DATA")),
                '<select id="Key" name="Key" class="MY-CLASS form-control" data-mine="MY DATA" ccm-passed-value="Two"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
            ),
            array(
                'select',
                array('Key', array('One' => 'First', 'Two' => 'Second'), 'Invalid'),
                '<select id="Key" name="Key" ccm-passed-value="Invalid" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
            ),
            array(
                'select',
                array('Key', array('One' => 'First', 'Two' => 'Second'), 'Two'),
                '<select id="Key" name="Key" ccm-passed-value="Two" class="form-control"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
                array('OtherField' => 'OtherValue'),
            ),
            array(
                'select',
                array('Key', array('One' => 'First', 'Two' => 'Second'), 'Two'),
                '<select id="Key" name="Key" ccm-passed-value="Two" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
                array('Key' => ''),
            ),
            array(
                'select',
                array('Key', array('One' => 'First', 'Two' => 'Second'), 'Two'),
                '<select id="Key" name="Key" ccm-passed-value="Two" class="form-control"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                array('Key' => 'One'),
            ),
            array(
                'select',
                array('Key', array('One' => 'First', 'Two' => 'Second'), 'Two'),
                '<select id="Key" name="Key" ccm-passed-value="Two" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
                array('Key' => 'Invalid'),
            ),
            array(
                'select',
                array('Key[subkey1][subkey2]', array('One' => 'First', 'Two' => 'Second'), 'Two'),
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" ccm-passed-value="Two" class="form-control"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
                array('Key' => 'One'),
            ),
            array(
                'select',
                array('Key[subkey1][subkey2]', array('One' => 'First', 'Two' => 'Second'), 'Two'),
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" ccm-passed-value="Two" class="form-control"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
                array('Key' => array('subkey1' => 'One')),
            ),
            array(
                'select',
                array('Key[subkey1][subkey2]', array('One' => 'First', 'Two' => 'Second'), 'Two'),
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" ccm-passed-value="Two" class="form-control"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                array('Key' => array('subkey1' => array('subkey2' => 'One'))),
            ),
            array(
                'select',
                array('Key[subkey1][subkey2]', array('One' => 'First', 'Two' => 'Second'), 'Two'),
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2]" ccm-passed-value="Two" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
                array('Key' => array('subkey1' => array('subkey2' => array('subkey3' => 'One')))),
            ),
            // selectMultiple
            array(
                'selectMultiple',
                array('Key', null),
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"></select>',
            ),
            array(
                'selectMultiple',
                array('Key', array()),
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"></select>',
            ),
            array(
                'selectMultiple',
                array('Key', array('One' => 'First', 'Two' => 'Second')),
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
            ),
            array(
                'selectMultiple',
                array('Key', array('One' => 'First', 'Two' => 'Second'), false),
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
            ),
            array(
                'selectMultiple',
                array('Key', array('One' => 'First', 'Two' => 'Second'), null),
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
            ),
            array(
                'selectMultiple',
                array('Key', array('One' => 'First', 'Two' => 'Second'), array()),
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
            ),
            array(
                'selectMultiple',
                array('Key', array('One' => 'First', 'Two' => 'Second'), false, array('data-mine' => 'MY DATA', 'class' => 'MY-CLASS')),
                '<select id="Key" name="Key[]" multiple="multiple" data-mine="MY DATA" class="MY-CLASS form-control"><option value="One">First</option><option value="Two">Second</option></select>',
            ),
            array(
                'selectMultiple',
                array('Key', array('One' => 'First', 'Two' => 'Second'), 'Two'),
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
            ),
            array(
                'selectMultiple',
                array('Key', array('One' => 'First', 'Two' => 'Second'), array('Two')),
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
            ),
            array(
                'selectMultiple',
                array('Key', array('One' => 'First', 'Two' => 'Second'), array('Invalid')),
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
            ),
            array(
                'selectMultiple',
                array('Key', array('One' => 'First', 'Two' => 'Second'), array('Two', 'One')),
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One" selected="selected">First</option><option value="Two" selected="selected">Second</option></select>',
            ),
            array(
                'selectMultiple',
                array('Key', array('One' => 'First', 'Two' => 'Second')),
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                array('Key' => 'One'),
            ),
            array(
                'selectMultiple',
                array('Key', array('One' => 'First', 'Two' => 'Second')),
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
                array('Key' => array()),
            ),
            array(
                'selectMultiple',
                array('Key', array('One' => 'First', 'Two' => 'Second')),
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                array('Key' => array('One')),
            ),
            array(
                'selectMultiple',
                array('Key', array('One' => 'First', 'Two' => 'Second'), 'Two'),
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                array('Key' => array('One')),
            ),
            array(
                'selectMultiple',
                array('Key', array('One' => 'First', 'Two' => 'Second')),
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One" selected="selected">First</option><option value="Two" selected="selected">Second</option></select>',
                array('Key' => array('One', 'Two')),
            ),
            array(
                'selectMultiple',
                array('Key', array('One' => 'First', 'Two' => 'Second'), 'Two'),
                '<select id="Key" name="Key[]" multiple="multiple" class="form-control"><option value="One" selected="selected">First</option><option value="Two" selected="selected">Second</option></select>',
                array('Key' => array('One', 'Two')),
            ),
            array(
                'selectMultiple',
                array('Key[subkey1][subkey2]', array('One' => 'First', 'Two' => 'Second'), 'Two'),
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2][]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
                array('Key' => array('One')),
            ),
            array(
                'selectMultiple',
                array('Key[subkey1][subkey2]', array('One' => 'First', 'Two' => 'Second'), 'Two'),
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2][]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two" selected="selected">Second</option></select>',
                array('Key' => array('subkey1' => array('One'))),
            ),
            array(
                'selectMultiple',
                array('Key[subkey1][subkey2]', array('One' => 'First', 'Two' => 'Second'), 'Two'),
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2][]" multiple="multiple" class="form-control"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                array('Key' => array('subkey1' => array('subkey2' => array('One')))),
            ),
            array(
                'selectMultiple',
                array('Key[subkey1][subkey2]', array('One' => 'First', 'Two' => 'Second'), 'Two'),
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2][]" multiple="multiple" class="form-control"><option value="One" selected="selected">First</option><option value="Two">Second</option></select>',
                array('Key' => array('subkey1' => array('subkey2' => array('subkey3' => 'One')))),
            ),
            array(
                'selectMultiple',
                array('Key[subkey1][subkey2]', array('One' => 'First', 'Two' => 'Second'), 'Two'),
                '<select id="Key[subkey1][subkey2]" name="Key[subkey1][subkey2][]" multiple="multiple" class="form-control"><option value="One">First</option><option value="Two">Second</option></select>',
                array('Key' => array('subkey1' => array('subkey2' => array('subkey3' => array('One'))))),
            ),
        );
    }
    /**
     * @dataProvider providerTestCreateElements
     */
    public function testCreateElements($method, array $args, $expected, array $post = array())
    {
        if (!empty($post)) {
            $_SERVER['REQUEST_METHOD'] = 'POST';
            foreach ($post as $k => $v) {
                $_REQUEST[$k] = $v;
                $_POST[$k] = $v;
            }
        }
        $calculated = call_user_func_array(array(static::$formHelper, $method), $args);
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
