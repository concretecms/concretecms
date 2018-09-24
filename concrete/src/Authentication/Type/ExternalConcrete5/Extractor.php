<?php
namespace Concrete\Core\Authentication\Type\ExternalConcrete5;

use OAuth\Common\Http\Uri\Uri;
use OAuth\UserData\Extractor\LazyExtractor;

class Extractor extends LazyExtractor
{
    const USER_PATH = '/ccm/api/v1/account/info';

    public function __construct()
    {
        parent::__construct(
            $this->getDefaultLoadersMap(),
            $this->getNormalizersMap(),
            $this->getSupports());
    }

    public function getSupports()
    {
        return [
            self::FIELD_EMAIL,
            self::FIELD_UNIQUE_ID,
            self::FIELD_USERNAME,
        ];
    }
    protected function getNormalizersMap()
    {
        return [
            self::FIELD_EMAIL => 'email',
            self::FIELD_FIRST_NAME => 'firstName',
            self::FIELD_LAST_NAME => 'lastName',
            self::FIELD_UNIQUE_ID => 'id',
            self::FIELD_USERNAME => 'username',
        ];
    }

    public function idNormalizer($data)
    {
        return isset($data['id']) ? (int) $data['id'] : null;
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
        return json_decode($this->service->request(self::USER_PATH), true)['data'];
    }
}
