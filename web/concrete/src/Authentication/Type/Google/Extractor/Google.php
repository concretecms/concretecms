<?php
namespace Concrete\Core\Authentication\Type\Google\Extractor;

use OAuth\Common\Http\Uri\Uri;
use OAuth\UserData\Extractor\LazyExtractor;

class Google extends LazyExtractor
{

    public function __construct()
    {
        parent::__construct(
            $this->getDefaultLoadersMap(),
            $this->getNormalizersMap(),
            $this->getSupports());
    }

    protected function getNormalizersMap()
    {
        return array(
            self::FIELD_EMAIL          => 'email',
            self::FIELD_FIRST_NAME     => 'firstName',
            self::FIELD_LAST_NAME      => 'lastName',
            self::FIELD_UNIQUE_ID      => 'id',
            self::FIELD_USERNAME       => 'email',
            self::FIELD_IMAGE_URL      => 'image',
            self::FIELD_VERIFIED_EMAIL => 'emailVerified',
            self::FIELD_EXTRA          => 'extras');
    }

    public function getSupports()
    {
        return array(
            self::FIELD_EMAIL,
            self::FIELD_FIRST_NAME,
            self::FIELD_LAST_NAME,
            self::FIELD_UNIQUE_ID,
            self::FIELD_USERNAME,
            self::FIELD_IMAGE_URL,
            self::FIELD_VERIFIED_EMAIL,
            self::FIELD_EXTRA);
    }

    public function extrasNormalizer($data)
    {
        $hd = array_get($data, 'hd', null);
        if (!$hd) {
            $email = $this->emailNormalizer($data);
            $pos = strrpos($email, '@');
            if ($pos !== false) {
                $hd = substr($email, $pos + 1);
            } else {
                $hd = null;
            }
        }
        return array('domain' => $hd);
    }

    public function imageNormalizer($data)
    {
        return array_get($data, 'picture', null);
    }

    public function idNormalizer($data)
    {
        return array_get($data, 'id', null);
    }

    public function emailVerifiedNormalizer($data)
    {
        return array_get($data, 'verified_email', false);
    }

    public function firstNameNormalizer($data)
    {
        return array_get($data, 'given_name', null);
    }

    public function lastNameNormalizer($data)
    {
        return array_get($data, 'family_name', null);
    }

    public function emailNormalizer($data)
    {
        return array_get($data, 'email', null);
    }

    public function profileLoader()
    {
        $url = new Uri('https://www.googleapis.com/oauth2/v1/userinfo');
        return json_decode($this->service->request($url), true);
    }

}
