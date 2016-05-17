<?php
namespace Concrete\Core\Authentication\Type\Community\Extractor;

use OAuth\Common\Http\Uri\Uri;
use OAuth\UserData\Extractor\LazyExtractor;

class Community extends LazyExtractor
{
    const USER_PATH = '/api/v1/-/user/';

    public function __construct()
    {
        parent::__construct(
            $this->getDefaultLoadersMap(),
            $this->getNormalizersMap(),
            $this->getSupports());
    }

    public function getSupports()
    {
        return array(
            self::FIELD_EMAIL,
            self::FIELD_FIRST_NAME,
            self::FIELD_LAST_NAME,
            self::FIELD_UNIQUE_ID,
            self::FIELD_USERNAME, );
    }
    protected function getNormalizersMap()
    {
        return array(
            self::FIELD_EMAIL => 'email',
            self::FIELD_FIRST_NAME => 'firstName',
            self::FIELD_LAST_NAME => 'lastName',
            self::FIELD_UNIQUE_ID => 'id',
            self::FIELD_USERNAME => 'username', );
    }

    public function idNormalizer($data)
    {
        return isset($data['id']) ? intval($data['id']) : null;
    }

    public function emailNormalizer($data)
    {
        return array_get($data, 'email', null);
    }

    public function firstNameNormalizer($data)
    {
        return array_get($data, 'first_name', null);
    }

    public function lastNameNormalizer($data)
    {
        return array_get($data, 'last_name', null);
    }

    public function usernameNormalizer($data)
    {
        return array_get($data, 'username', null);
    }

    public function profileLoader()
    {
        $uri = new Uri(\Config::get('concrete.urls.concrete5_secure') . self::USER_PATH);

        return json_decode($this->service->request($uri), true);
    }
}
