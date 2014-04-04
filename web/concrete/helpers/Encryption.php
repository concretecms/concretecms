<?
namespace Concrete\Helper;
class Encryption {

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