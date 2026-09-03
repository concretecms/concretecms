<?php

namespace Concrete\Tests\Api\OAuth\Grant;

use Concrete\Core\Api\OAuth\Grant\RefreshTokenGrant;
use Concrete\Core\Api\OAuth\UserStatusValidator;
use Concrete\Tests\TestCase;
use Concrete\TestHelpers\Api\OAuth\InMemoryAccessTokenRepository;
use Concrete\TestHelpers\Api\OAuth\InMemoryRefreshTokenRepository;
use Defuse\Crypto\Key;
use GuzzleHttp\Psr7\ServerRequest;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Mockery as M;
use ReflectionMethod;

/**
 * Refresh tokens live for a month by default, so a token issued before a user was deactivated is the
 * longest-lived way back into the API.
 *
 * The refresh token in these tests is always well formed and unexpired, and is presented by the client
 * it was issued to - everything the stock League grant checks. Only the state of the account differs.
 *
 * @covers \Concrete\Core\Api\OAuth\Grant\RefreshTokenGrant
 */
class RefreshTokenGrantTest extends TestCase
{

    const CLIENT_ID = 'client-id';
    const REFRESH_TOKEN_ID = 'refresh-token-id';
    const ACCESS_TOKEN_ID = 'access-token-id';
    const USER_ID = 42;

    /** @var \Concrete\Core\Api\OAuth\Grant\RefreshTokenGrant */
    protected $grant;

    /** @var \Concrete\TestHelpers\Api\OAuth\InMemoryRefreshTokenRepository */
    protected $refreshTokenRepository;

    /** @var \Concrete\TestHelpers\Api\OAuth\InMemoryAccessTokenRepository */
    protected $accessTokenRepository;

    /** @var \Concrete\Core\Api\OAuth\UserStatusValidator|\Mockery\Mock */
    protected $userStatusValidator;

    public function setUp(): void
    {
        parent::setUp();

        $this->refreshTokenRepository = new InMemoryRefreshTokenRepository(self::REFRESH_TOKEN_ID);
        $this->accessTokenRepository = new InMemoryAccessTokenRepository(self::ACCESS_TOKEN_ID);
        $this->userStatusValidator = M::mock(UserStatusValidator::class);

        $this->grant = new RefreshTokenGrant(
            $this->refreshTokenRepository,
            M::mock(UserRepositoryInterface::class),
            $this->userStatusValidator
        );
        $this->grant->setAccessTokenRepository($this->accessTokenRepository);
        $this->grant->setEncryptionKey(Key::createNewRandomKey());
    }

    /**
     * Build the encrypted refresh token a client would present, using the grant's own crypto.
     *
     * @param array $overrides
     *
     * @return string
     */
    protected function encryptedRefreshToken(array $overrides = [])
    {
        $payload = array_merge([
            'client_id' => self::CLIENT_ID,
            'refresh_token_id' => self::REFRESH_TOKEN_ID,
            'access_token_id' => self::ACCESS_TOKEN_ID,
            'scopes' => [],
            'user_id' => self::USER_ID,
            'expire_time' => time() + 3600,
        ], $overrides);

        $encrypt = new ReflectionMethod($this->grant, 'encrypt');
        $encrypt->setAccessible(true);

        return $encrypt->invoke($this->grant, json_encode($payload));
    }

    /**
     * Present a refresh token to the grant the way the token endpoint would.
     *
     * @param string $encryptedRefreshToken
     *
     * @return array the decoded refresh token payload
     */
    protected function presentRefreshToken($encryptedRefreshToken)
    {
        $request = (new ServerRequest('POST', 'https://example.com/oauth/2.0/token'))
            ->withParsedBody([
                'grant_type' => 'refresh_token',
                'client_id' => self::CLIENT_ID,
                'refresh_token' => $encryptedRefreshToken,
            ]);

        $validate = new ReflectionMethod($this->grant, 'validateOldRefreshToken');
        $validate->setAccessible(true);

        return $validate->invoke($this->grant, $request, self::CLIENT_ID);
    }

    /**
     * The check must not break the normal case.
     */
    public function testRefreshTokenForActiveUserIsAccepted()
    {
        $this->userStatusValidator->shouldReceive('isValid')->once()->with(self::USER_ID)->andReturn(true);

        $payload = $this->presentRefreshToken($this->encryptedRefreshToken());

        $this->assertSame(self::REFRESH_TOKEN_ID, $payload['refresh_token_id']);
        $this->assertSame(self::USER_ID, $payload['user_id']);
        $this->assertSame([], $this->refreshTokenRepository->revoked, 'A valid refresh token must not be revoked.');
        $this->assertSame([], $this->accessTokenRepository->revoked, 'A valid access token must not be revoked.');
    }

    /**
     * Findings #1 and #3: the account behind the token is no longer allowed to authenticate, so the
     * token must not be exchangeable for a fresh access token.
     */
    public function testRefreshTokenForInactiveUserIsRejected()
    {
        $this->userStatusValidator->shouldReceive('isValid')->with(self::USER_ID)->andReturn(false);

        try {
            $this->presentRefreshToken($this->encryptedRefreshToken());
            $this->fail('A refresh token belonging to an inactive user was accepted.');
        } catch (OAuthServerException $e) {
            $this->assertSame('invalid_request', $e->getErrorType());
            $this->assertSame(401, $e->getHttpStatusCode());
            $this->assertSame('Token is not linked to an active user', $e->getHint());
        }
    }

    /**
     * Finding #4: the rejection threw before the parent grant reached its own revocation step, so the
     * refused refresh token - and the access token it was paired with - were both left live.
     */
    public function testRejectedRefreshTokenIsRevoked()
    {
        $this->userStatusValidator->shouldReceive('isValid')->with(self::USER_ID)->andReturn(false);

        try {
            $this->presentRefreshToken($this->encryptedRefreshToken());
        } catch (OAuthServerException $e) {
            // Asserted in testRefreshTokenForInactiveUserIsRejected().
        }

        $this->assertSame(
            [self::REFRESH_TOKEN_ID],
            $this->refreshTokenRepository->revoked,
            'The refused refresh token must be revoked rather than left retryable.'
        );
        $this->assertSame(
            [self::ACCESS_TOKEN_ID],
            $this->accessTokenRepository->revoked,
            'The access token the refresh token was paired with must be revoked too - it is still live otherwise.'
        );
    }

    /**
     * Finding #4, the consequence: without revocation the refused token could be retried indefinitely,
     * and would start working again the moment the account was reactivated.
     *
     * Here the account IS reactivated between the two attempts, and the token still fails - because it
     * no longer exists, not because the user is inactive.
     */
    public function testRejectedRefreshTokenCannotBeRetriedEvenIfTheAccountIsReactivated()
    {
        $encrypted = $this->encryptedRefreshToken();

        // First attempt: the account is deactivated, so the token is refused and revoked.
        $this->userStatusValidator->shouldReceive('isValid')->once()->with(self::USER_ID)->andReturn(false);

        try {
            $this->presentRefreshToken($encrypted);
            $this->fail('A refresh token belonging to an inactive user was accepted.');
        } catch (OAuthServerException $e) {
            $this->assertSame('Token is not linked to an active user', $e->getHint());
        }

        // Second attempt with the very same token, with the account now active again.
        $this->userStatusValidator->shouldReceive('isValid')->with(self::USER_ID)->andReturn(true);

        try {
            $this->presentRefreshToken($encrypted);
            $this->fail('A revoked refresh token was accepted on a retry.');
        } catch (OAuthServerException $e) {
            $this->assertSame(
                'Token has been revoked',
                $e->getHint(),
                'The retry must fail because the token is gone, not merely because the user was inactive.'
            );
        }
    }

    /**
     * The user check must run in addition to the stock checks, not instead of them: a token presented
     * by a client it was not issued to must still be refused, and must not be revoked on the strength
     * of another client's say-so.
     */
    public function testTokenPresentedByTheWrongClientIsStillRejected()
    {
        $this->userStatusValidator->shouldNotReceive('isValid');

        $encrypted = $this->encryptedRefreshToken(['client_id' => 'a-different-client']);

        try {
            $this->presentRefreshToken($encrypted);
            $this->fail('A refresh token issued to another client was accepted.');
        } catch (OAuthServerException $e) {
            $this->assertSame('Token is not linked to client', $e->getHint());
        }

        $this->assertSame(
            [],
            $this->refreshTokenRepository->revoked,
            'A token must not be revoked because an unrelated client presented it.'
        );
    }
}
