<?php

namespace Concrete\Tests\Api\OAuth\Server;

use Concrete\Core\Api\OAuth\Server\ClaimsSetFactory;
use Concrete\Core\Api\OAuth\Server\IdTokenResponse;
use Concrete\Core\Api\OAuth\UserStatusValidator;
use Concrete\Core\Site\Service;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Tests\TestCase;
use DateTimeImmutable;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OpenIdConnectClaims\ClaimsSet;
use Mockery as M;
use ReflectionMethod;

/**
 * An id_token is an assertion to a relying party that the user authenticated. Unlike an access token
 * it is not re-validated against this site on use, so it must not be minted for an account that can no
 * longer log in - the relying party would simply sign the user in.
 *
 * Auth codes are redeemable for a day, which is ample time for an account to be deactivated in between
 * the authorization and the redemption.
 *
 * @covers \Concrete\Core\Api\OAuth\Server\IdTokenResponse
 */
class IdTokenResponseTest extends TestCase
{

    const USER_ID = 42;
    const CLIENT_ID = 'client-id';

    /**
     * @var string
     */
    protected static $privateKeyPem;

    /** @var \Concrete\Core\Api\OAuth\UserStatusValidator|\Mockery\Mock */
    protected $userStatusValidator;

    /** @var \Concrete\Core\Api\OAuth\Server\IdTokenResponse */
    protected $response;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $resource = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        openssl_pkey_export($resource, self::$privateKeyPem);
    }

    public function setUp(): void
    {
        parent::setUp();

        $site = M::mock(Service::class);
        // No site: createIdToken() falls back to a literal issuer, which keeps this test off the
        // site/locale machinery entirely.
        $site->shouldReceive('getSite')->andReturn(null);

        $claimsSet = new ClaimsSet();
        $claimsSet->setIdentifier(self::USER_ID);

        $claimFactory = M::mock(ClaimsSetFactory::class);
        $claimFactory->shouldReceive('createFromUserInfo')->andReturn($claimsSet);

        $userInfoRepository = M::mock(UserInfoRepository::class);
        $userInfoRepository->shouldReceive('getByID')->with(self::USER_ID)->andReturn(M::mock(UserInfo::class));

        $this->userStatusValidator = M::mock(UserStatusValidator::class);

        $this->response = new IdTokenResponse(
            $site,
            $claimFactory,
            $userInfoRepository,
            $this->userStatusValidator
        );
        $this->response->setPrivateKey(new CryptKey(self::$privateKeyPem));
    }

    /**
     * Build the access token the response type is asked to describe.
     *
     * @param string[] $scopeIdentifiers
     *
     * @return \League\OAuth2\Server\Entities\AccessTokenEntityInterface
     */
    protected function accessToken(array $scopeIdentifiers)
    {
        $scopes = [];
        foreach ($scopeIdentifiers as $identifier) {
            $scope = M::mock(ScopeEntityInterface::class);
            $scope->shouldReceive('getIdentifier')->andReturn($identifier);
            $scopes[] = $scope;
        }

        $client = M::mock(ClientEntityInterface::class);
        $client->shouldReceive('getIdentifier')->andReturn(self::CLIENT_ID);

        $accessToken = M::mock(AccessTokenEntityInterface::class);
        $accessToken->shouldReceive('getScopes')->andReturn($scopes);
        $accessToken->shouldReceive('getUserIdentifier')->andReturn(self::USER_ID);
        $accessToken->shouldReceive('getClient')->andReturn($client);
        $accessToken->shouldReceive('getExpiryDateTime')->andReturn(new DateTimeImmutable('+1 hour'));

        return $accessToken;
    }

    /**
     * @param \League\OAuth2\Server\Entities\AccessTokenEntityInterface $accessToken
     *
     * @return array
     */
    protected function getExtraParams(AccessTokenEntityInterface $accessToken)
    {
        $method = new ReflectionMethod($this->response, 'getExtraParams');
        $method->setAccessible(true);

        return $method->invoke($this->response, $accessToken);
    }

    /**
     * Decode a JWT payload without verifying it - these tests care about the claims, not the signature.
     *
     * @param string $jwt
     *
     * @return array
     */
    protected function decodeClaims($jwt)
    {
        $segments = explode('.', $jwt);
        $this->assertCount(3, $segments, 'The id_token should be a well formed JWT.');

        return json_decode(base64_decode(strtr($segments[1], '-_', '+/')), true);
    }

    /**
     * Finding #2: the auth code grant never consults the user repository, so a code issued before the
     * account was deactivated stayed redeemable - and handed back a signed assertion that the user had
     * just authenticated.
     */
    public function testIdTokenIsNotIssuedForInactiveUser()
    {
        $this->userStatusValidator->shouldReceive('isValid')->once()->with(self::USER_ID)->andReturn(false);

        $params = $this->getExtraParams($this->accessToken(['openid', 'site']));

        $this->assertArrayNotHasKey(
            'id_token',
            $params,
            'An id_token must not be minted for an account that can no longer log in.'
        );
    }

    /**
     * The check must not break OpenID Connect for everyone else.
     */
    public function testIdTokenIsIssuedForActiveUser()
    {
        $this->userStatusValidator->shouldReceive('isValid')->once()->with(self::USER_ID)->andReturn(true);

        $params = $this->getExtraParams($this->accessToken(['openid', 'site']));

        $this->assertArrayHasKey('id_token', $params, 'An active user must still receive an id_token.');

        $claims = $this->decodeClaims($params['id_token']);
        $this->assertSame((string) self::USER_ID, $claims['sub']);
        $this->assertSame(self::CLIENT_ID, $claims['aud']);
    }

    /**
     * A plain API token request carries no openid scope and never produced an id_token to begin with.
     * It must not start paying for a user lookup either.
     */
    public function testNonOidcRequestIsUnaffected()
    {
        $this->userStatusValidator->shouldNotReceive('isValid');

        $params = $this->getExtraParams($this->accessToken(['site']));

        $this->assertArrayNotHasKey('id_token', $params);
    }
}
