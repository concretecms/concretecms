<?php
namespace Concrete\Core\Form\Service;

use Loader;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validator\String\EmailValidator;

/**
 * \@package    Helpers
 * @subpackage Validation
 *
 * @author     Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Helper functions to use with validating submitting forms.
 *
 * \@package    Helpers
 * @subpackage Validation
 *
 * @author     Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class Validation
{
    /**
     */
    const VALID_NOT_EMPTY = 1;
    const VALID_EMAIL = 2;
    const VALID_INTEGER = 3;
    const VALID_INTEGER_REQUIRED = 4;
    const VALID_UPLOADED_IMAGE = 10;
    const VALID_UPLOADED_IMAGE_REQUIRED = 11;
    const VALID_UPLOADED_FILE = 20;
    const VALID_UPLOADED_FILE_REQUIRED = 25;
    const VALID_TOKEN = 30;
    const VALID_FIELD_INVALID = 99;
    protected $fields = array();
    protected $fieldsInvalid = array();
    protected $data = array();
    protected $files = array();
    protected $error;

    public function __construct()
    {
        $this->error = Loader::helper('validation/error');
    }

    /**
     * Adds a test to a field to ensure that, if set, it is a valid uploaded image.
     *
     * @param string $field
     * @param string $errorMsg
     * @param bool   $emptyIsOk Tells whether this can be submitted as empty (e.g. the validation tests only run if someone is actually submitted in the post.)
     */
    public function addUploadedImage($field, $errorMsg = null, $emptyIsOk = true)
    {
        $const = ($emptyIsOk) ? self::VALID_UPLOADED_IMAGE : self::VALID_UPLOADED_IMAGE_REQUIRED;
        $this->addRequired($field, $errorMsg, $const);
    }

    /**
     * Adds a required field to the form helper object. This will then be typically used in conjunction with the test() method to see
     * if the test is passed.
     *
     * @param string $field
     * @param string $errorMsg
     * @param string $validate test to validate against
     */
    public function addRequired($field, $errorMsg = null, $validate = self::VALID_NOT_EMPTY)
    {
        $obj = new \stdClass;
        $obj->message = ($errorMsg == null) ? t('Field "%s" is invalid', $field) : $errorMsg;
        $obj->field = $field;
        $obj->validate = $validate;
        $this->fields[] = $obj;
    }

    /**
     * Adds a test to a field to ensure that, if set, it is a valid uploaded file.
     *
     * @param string $field
     * @param string $errorMsg
     * @param bool   $emptyIsOk Tells whether this can be submitted as empty (e.g. the validation tests only run if someone is actually submitted in the post.)
     */
    public function addUploadedFile($field, $errorMsg = null, $emptyIsOk = true)
    {
        $const = ($emptyIsOk) ? self::VALID_UPLOADED_FILE : self::VALID_UPLOADED_FILE_REQUIRED;
        $this->addRequired($field, $errorMsg, $const);
    }

    /**
     * Adds a required field and tests that it is integer only.
     *
     * @param string $field
     * @param string $errorMsg
     * @param bool   $emptyIsOk Tells whether this can be submitted as empty (e.g. the validation tests only run if someone is actually submitted in the post.)
     */
    public function addInteger($field, $errorMsg = null, $emptyIsOk = true)
    {
        $const = ($emptyIsOk) ? self::VALID_INTEGER : self::VALID_INTEGER_REQUIRED;
        $this->addRequired($field, $errorMsg, $const);
    }

    public function addRequiredToken($value, $errorMsg = null)
    {
        $obj = new \stdClass();
        $vt = Loader::helper('validation/token');
        $obj->message = ($errorMsg == null) ? $vt->getErrorMessage() : $errorMsg;
        $obj->value = $value;
        $obj->validate = self::VALID_TOKEN;
        $this->fields[] = $obj;
    }

    /**
     * Adds a required email address to the suite of tests to be run.
     *
     * @param string $field
     * @param string $errorMsg
     */
    public function addRequiredEmail($field, $errorMsg = null)
    {
        $this->addRequired($field, $errorMsg, self::VALID_EMAIL);
    }

    /**
     * Sets the data files array.
     */
    public function setFiles()
    {
        $this->files = $_FILES;
    }

    /**
     * An associative array that we setup to validate against. Typical usage is $val->setData($_POST);.
     *
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function invalidate($message)
    {
        $f = new \stdClass();
        $f->message = $message;
        $this->fieldsInvalid[] = $f;
    }

    /**
     * After the validation error helper has been setup, the test() method ensures that all fields that require validation
     * pass. Returns the number of invalid fields (0 = success).
     *
     * @return int
     */
    public function test()
    {
        $app = Application::getFacadeApplication();
        $val = $app->make('helper/validation/strings');
        $num = $app->make('helper/validation/numbers');
        $fil = $app->make('helper/validation/file');

        // loop through all the fields in the array, and run whatever validation
        // the validate parameter says is required
        foreach ($this->fields as $f) {
            $validate = $f->validate;
            $field = $f->field;
            $fieldValue = isset($this->data[$field]) ? $this->data[$field] : null;
            switch ($validate) {
                case self::VALID_NOT_EMPTY:
                    if (!$val->notempty($fieldValue)) {
                        $this->fieldsInvalid[] = $f;
                    }
                    break;
                case self::VALID_TOKEN:
                    $vt = $app->make('helper/validation/token');
                    if (!$vt->validate($f->value)) {
                        $this->fieldsInvalid[] = $f;
                    }
                    break;
                case self::VALID_INTEGER:
                    if ((!$num->integer($fieldValue)) && ($val->notempty($fieldValue))) {
                        $this->fieldsInvalid[] = $f;
                    }
                    break;
                case self::VALID_INTEGER_REQUIRED:
                    if (!$num->integer($fieldValue)) {
                        $this->fieldsInvalid[] = $f;
                    }
                    break;
                case self::VALID_UPLOADED_IMAGE:
                    if ((!$fil->image($this->files[$field]['tmp_name'])) && ($this->files[$field]['tmp_name'] != '')) {
                        $this->fieldsInvalid[] = $f;
                    }
                    break;
                case self::VALID_UPLOADED_IMAGE_REQUIRED:
                    if (!$fil->image($this->files[$field]['tmp_name'])) {
                        $this->fieldsInvalid[] = $f;
                    }
                    break;
                case self::VALID_UPLOADED_FILE:
                    if ((!$fil->file($this->files[$field]['tmp_name'])) && ($this->files[$field]['tmp_name'] != '')) {
                        $this->fieldsInvalid[] = $f;
                    }
                    break;
                case self::VALID_UPLOADED_FILE_REQUIRED:
                    if (!$fil->file($this->files[$field]['tmp_name'])) {
                        $this->fieldsInvalid[] = $f;
                    }
                    break;
                case self::VALID_EMAIL:
                    if (!$app->make(EmailValidator::class)->isValid($fieldValue)) {
                        $this->fieldsInvalid[] = $f;
                    }
                    break;
            }
        }

        $this->setErrorsFromInvalidFields();

        return count($this->fieldsInvalid) == 0;
    }

    /**
     */
    protected function setErrorsFromInvalidFields()
    {
        foreach ($this->fieldsInvalid as $f) {
            if (isset($f->field)) {
                $this->error->add($f->message, $f->field);
            } else {
                $this->error->add($f->message);
            }
        }
    }


    public function getError()
    {
        return $this->error;
    }
}
