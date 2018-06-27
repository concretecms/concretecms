<?php

namespace Concrete\Core\Validator\String;

use ArrayAccess;
use Concrete\Core\Validator\AbstractTranslatableValidator;
use Egulias\EmailValidator\EmailValidator as EguliasEmailValidator;
use InvalidArgumentException;

/**
 * Validate the length of a string.
 */
class EmailValidator extends AbstractTranslatableValidator
{
    /**
     * The email address is not valid.
     *
     * @var int
     */
    const E_INVALID_ADDRESS = 1;

    /**
     * Should we test the MX record to see if the domain is valid?
     *
     * @var bool
     */
    protected $testMXRecord;

    /**
     * Should email address warnings be considered as errors?
     *
     * @var bool
     */
    protected $strict;

    /**
     * The instance of Egulias\EmailValidator to be used.
     * This is private because in future we may switch to another library.
     *
     * @var \Egulias\EmailValidator\EmailValidator|null
     */
    private $eguliasEmailValidator;

    /**
     * EmailValidator constructor.
     *
     * @param bool $testMXRecord Should we test the MX record to see if the domain is valid?
     * @param bool $strict Should email address warnings be considered as errors?
     */
    public function __construct($testMXRecord = false, $strict = false)
    {
        $this
            ->setTestMXRecord($testMXRecord)
            ->setStrict($strict)
        ;
        $this->setRequirementString(
            self::E_INVALID_ADDRESS,
            function (EmailValidator $validator, $code) {
                return t('The email address must be valid.');
            }
        );
        $this->setErrorString(
            self::E_INVALID_ADDRESS,
            function (EmailValidator $validator, $code, $mixed) {
                return t('The email address "%s" is not valid.', $mixed);
            }
        );
    }

    /**
     * Should we test the MX record to see if the domain is valid?
     *
     * @return bool
     */
    public function isTestMXRecord()
    {
        return $this->testMXRecord;
    }

    /**
     * Should we test the MX record to see if the domain is valid?
     *
     * @param bool $testMXRecord
     *
     * @return $this
     */
    public function setTestMXRecord($testMXRecord)
    {
        $this->testMXRecord = (bool) $testMXRecord;

        return $this;
    }

    /**
     * Should email address warnings be considered as errors?
     *
     * @return bool
     */
    public function isStrict()
    {
        return $this->strict;
    }

    /**
     * Should email address warnings be considered as errors?
     *
     * @param bool $strict
     *
     * @return $this
     */
    public function setStrict($strict)
    {
        $this->strict = (bool) $strict;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\ValidatorInterface::isValid()
     */
    public function isValid($mixed, ArrayAccess $error = null)
    {
        if ($mixed !== null && !is_string($mixed)) {
            throw new InvalidArgumentException(t('Invalid type supplied to validator.'));
        }
        if ($this->checkEmail($mixed) === false) {
            if ($error && $message = $this->getErrorString(self::E_INVALID_ADDRESS, $mixed)) {
                $error[] = $message;
            }

            return false;
        }

        return true;
    }

    /**
     * Actually check if an email address is valid.
     *
     * @param string|mixed $mixed
     *
     * @return bool
     */
    protected function checkEmail($mixed)
    {
        $result = false;
        if (is_string($mixed) && $mixed !== '') {
            $eev = $this->getEguliasEmailValidator();
            $testMX = $this->isTestMXRecord();
            if ($eev->isValid($mixed, $testMX, $this->isStrict())) {
                if ($testMX) {
                    $result = !in_array(EguliasEmailValidator::DNSWARN_NO_RECORD, $eev->getWarnings(), true);
                } else {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * Get the instance of Egulias\EmailValidator to be used.
     * This is private because in future we may switch to another library.
     *
     * @return \Egulias\EmailValidator\EmailValidator
     */
    private function getEguliasEmailValidator()
    {
        if ($this->eguliasEmailValidator === null) {
            $this->eguliasEmailValidator = new EguliasEmailValidator();
        }

        return $this->eguliasEmailValidator;
    }
}
