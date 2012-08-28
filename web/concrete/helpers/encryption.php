<?php

/**
 * Encryption helper
 * 
 * A wrapper class for mcrypt.
 * 
 * Used as follows:
 * <code>
 * $enc = Loader::helper('encryption');
 * $string = 'This is some random text.';
 * $crypted = $enc->encrypt($string);
 * echo $enc->decrypt($crypted);
 * </code>     
 *   
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */ 

defined('C5_EXECUTE') or die("Access Denied.");

class EncryptionHelper extends Concrete5_Helper_Encryption {

}
