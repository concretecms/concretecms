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

class Concrete5_Helper_Encryption {

	/** 
	 * Takes encrypted text and decrypts it.
	 * @param string $text
	 * @return string $text
	 */
	 
    static public function decrypt($text)
    {
        if (function_exists('mcrypt_decrypt')) {
            $iv_size = mcrypt_get_iv_size(MCRYPT_XTEA, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $len = mcrypt_get_key_size(MCRYPT_XTEA, MCRYPT_MODE_ECB);
            $text = trim(mcrypt_decrypt(MCRYPT_XTEA, substr(Config::get('SECURITY_TOKEN_ENCRYPTION'), 0, $len), base64_decode($text), MCRYPT_MODE_ECB, $iv));
        }
        return $text;
    }
    
	/** 
	 * Takes un-encrypted text and encrypts it.
	 * @param string $text
	 * @return string $text
	 */
	 
    static public function encrypt($text)
    {
        if (function_exists('mcrypt_encrypt')) {
            $iv_size = mcrypt_get_iv_size(MCRYPT_XTEA, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $len = mcrypt_get_key_size(MCRYPT_XTEA, MCRYPT_MODE_ECB);
            $text = base64_encode(mcrypt_encrypt(MCRYPT_XTEA, substr(Config::get('SECURITY_TOKEN_ENCRYPTION'), 0, $len), $text, MCRYPT_MODE_ECB, $iv));
        }
        return $text;
    }
    
	/** 
	 * Function to see if mcrypt is installed
	 * @return bool
	 */
    
    public function isAvailable() {
    	return function_exists('mcrypt_encrypt');
    }

}
