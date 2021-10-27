<?php
namespace Concrete\Core\Authentication\Type\ExternalConcrete;

use Lcobucci\JWT\{
    Encoding\JoseEncoder,
    Parser,
    Token\Parser as TokenParser
};
use OAuth\Common\Http\Uri\Uri;
use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\UserData\Extractor\LazyExtractor;

class Extractor extends LazyExtractor
{
    const USER_PATH = '/ccm/api/v1/account/info';

    protected $service;

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
        if (isset($data['claims'])) {
            return $this->claim(array_get($data, 'claims.sub'));
        }

        return isset($data['id']) ? (int) $data['id'] : null;
    }

    public function emailNormalizer($data)
    {
        if (isset($data['claims'])) {
            return $this->claim(array_get($data, 'claims.email'));
        }

        return array_get($data, 'email', null);
    }

    public function firstNameNormalizer($data)
    {
        if (isset($data['claims'])) {
            return $this->claim(array_get($data, 'claims.given_name'));
        }

        return array_get($data, 'first_name', null);
    }

    public function lastNameNormalizer($data)
    {
        if (isset($data['claims'])) {
            return $this->claim(array_get($data, 'claims.family_name'));
        }

        return array_get($data, 'last_name', null);
    }

    public function usernameNormalizer($data)
    {
        if (isset($data['claims'])) {
            return $this->claim(array_get($data, 'claims.preferred_username'));
        }

        return array_get($data, 'username', null);
    }

    /**
     * Convert a claim into its raw value
     *
     * @param \Lcobucci\JWT\Claim|string $claim
     *
     * @return string
     */
    protected function claim($claim = null)
    {
        if (!$claim) {
            return null;
        }

        if (is_string($claim)) {
            return $claim;
        }

        return $claim->getValue();
    }

    /**
     * Load the external Concrete profile, either from id_token or through the API
     *
     * @return array
     *
     * @throws \OAuth\Common\Exception\Exception
     * @throws \OAuth\Common\Storage\Exception\TokenNotFoundException
     * @throws \OAuth\Common\Token\Exception\ExpiredTokenException
     */
    public function profileLoader()
    {
        $idTokenString = null;
        $token = $this->service->getStorage()->retrieveAccessToken($this->service->service());
        if ($token instanceof StdOAuth2Token) {
            $idTokenString = array_get($token->getExtraParams(), 'id_token');
        }

        // If we don't have a proper ID token, let's just fetch the data from the API
        if (!$idTokenString) {
            return json_decode($this->service->request(self::USER_PATH), true)['data'];
        }

        if (class_exists(TokenParser::class)) {
            $decoder = new TokenParser(new JoseEncoder());
            $token = $decoder->parse($idTokenString);
            $claims = $token->claims()->all();
        } else {
            $decoder = new Parser();
            $token = $decoder->parse($idTokenString);
            $claims = $token->getClaims();
        }

        return [
            'claims' => $claims
        ];
    }
}
