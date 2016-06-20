<?php
namespace Concrete\Core\Encryption;

use Config;

class EncryptionService
{
    /**
     * Takes encrypted text and decrypts it.
     *
     * @param string $text
     *
     * @return string $text
     */
    public static function decrypt($text)
    {
        if (function_exists('mcrypt_decrypt')) {
            $iv_size = mcrypt_get_iv_size(MCRYPT_XTEA, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $len = mcrypt_get_key_size(MCRYPT_XTEA, MCRYPT_MODE_ECB);

            /** @var \Concrete\Core\Config\Repository\Repository $config */
            $config = \Core::make('config/database');
            $text = trim(mcrypt_decrypt(MCRYPT_XTEA, substr($config->get('concrete.security.token.encryption'), 0, $len), base64_decode($text), MCRYPT_MODE_ECB, $iv));
        }

        return $text;
    }

    /**
     * Takes un-encrypted text and encrypts it.
     *
     * @param string $text
     *
     * @return string $text
     */
    public static function encrypt($text)
    {
        if (function_exists('mcrypt_encrypt')) {
            $iv_size = mcrypt_get_iv_size(MCRYPT_XTEA, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $len = mcrypt_get_key_size(MCRYPT_XTEA, MCRYPT_MODE_ECB);

            /** @var \Concrete\Core\Config\Repository\Repository $config */
            $config = \Core::make('config/database');
            $text = base64_encode(mcrypt_encrypt(MCRYPT_XTEA, substr($config->get('concrete.security.token.encryption'), 0, $len), $text, MCRYPT_MODE_ECB, $iv));
        }

        return $text;
    }

    /**
     * Function to see if mcrypt is installed.
     *
     * @return bool
     */
    public function isAvailable()
    {
        return function_exists('mcrypt_encrypt');
    }
}
