<?php

class ConcreteValidationTest extends PHPUnit_Framework_TestCase {

    /**
     * @var ConcreteValidation
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = Loader::helper('concrete/validation');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

	 public function testObjectClass() {
		 $this->assertTrue($this->object instanceof ConcreteValidationHelper);
	 }

    public function testPassword() {
        $false = array();
        $false[] = ''; //minimum length
        $false[] = ' '; //min length with space
        $false[] = '1a'; //min length alpha num

        $false[] = '012345678901234567890123456789012345678901234567890123456789abcde'; //max is 64, so this is 65

        $false[] = 'ab cdefg'; //regex testing
        $false[] = 'ab>cdefg';
        $false[] = 'ab<cdefg';
        $false[] = 'ab\'cdefg';
        $false[] = 'ab"cdefg';
        $false[] = 'ab\cdefg';
        //$false[] = 'ab/cdefg'; //true?

        foreach ($false as $string) {
            $this->assertFalse($this->object->password($string));
        }

        $true = array();
        $true[] = '1234567890';
        $true[] = 'abcdefghijklmnopqrstuvwxyz';
        $true[] = '!@#$%^&*()_+-=';

        foreach ($true as $string) {
            $this->assertTrue($this->object->password($string));
        }
    }

    public function testUsername() {
        $false = array();
        $false[] = ''; //minimum length
        $false[] = ' '; //min length with space
        $false[] = '1a'; //min length alpha num

        $false[] = '012345678901234567890123456789012345678901234567890123456789abcde'; //max is 64, so this is 65

        $false[] = 'ab cdefg'; //regex testing
        $false[] = 'ab>cdefg';
        $false[] = 'ab<cdefg';
        $false[] = 'ab\'cdefg';
        $false[] = 'ab"cdefg';
        $false[] = 'ab\cdefg';
        $false[] = '!@#$%^&*()_+-=';

        foreach ($false as $string) {
            $this->assertFalse($this->object->username($string));
        }

        $true = array();
        $true[] = '1234567890';
        $true[] = 'abcdefghijklmnopqrstuvwxyz';

        foreach ($true as $string) {
            $this->assertTrue($this->object->username($string));
        }
    }

}

?>
