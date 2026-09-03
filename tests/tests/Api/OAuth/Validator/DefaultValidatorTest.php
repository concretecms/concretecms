<?php

namespace Concrete\Tests\Api\OAuth\Validator;

use Concrete\Core\Api\OAuth\UserStatusValidator;
use Concrete\Core\Api\OAuth\Validator\DefaultValidator;
use Concrete\Core\Application\Application;
use Concrete\Core\User\User;
use Concrete\Tests\TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator;
use League\OAuth2\Server\Exception\OAuthServerException;
use Mockery as M;

/**
 * Every authenticated API request passes through this validator, so it is where a token issued before
 * a user was deactivated has to be stopped.
 *
 * The bearer token in these tests is always perfectly valid - correctly signed, unexpired, and still
 * present in OAuth2AccessToken. That is the whole point: BearerTokenValidator is happy, and without a
 * separate check on the account the request would be served.
 *
 * @covers \Concrete\Core\Api\OAuth\Validator\DefaultValidator
 */
class DefaultValidatorTest extends TestCase
{

    /** @var \League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator|\Mockery\Mock */
    protected $bearerTokenValidator;

    /** @var \Concrete\Core\Api\OAuth\UserStatusValidator|\Mockery\Mock */
    protected $userStatusValidator;

    /** @var \Concrete\Core\Api\OAuth\Validator\DefaultValidator */
    protected $validator;

    public function setUp(): void
    {
        parent::setUp();

        $this->bearerTokenValidator = M::mock(BearerTokenValidator::class);
        $this->userStatusValidator = M::mock(UserStatusValidator::class);

        $app = M::mock(Application::class);
        $app->shouldReceive('make')->andReturn(M::mock(User::class));

        $this->validator = new DefaultValidator($this->bearerTokenValidator, $app, $this->userStatusValidator);
    }

    /**
     * Build the request BearerTokenValidator hands back once it has accepted a token.
     *
     * @param string $userIdentifier the "sub" claim; empty for client credentials tokens
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function validatedRequest($userIdentifier)
    {
        return (new ServerRequest('GET', 'https://example.com/api/1.0/system/info'))
            ->withAttribute('oauth_access_token_id', 'access-token-id')
            ->withAttribute('oauth_client_id', 'client-id')
            ->withAttribute('oauth_user_id', $userIdentifier)
            ->withAttribute('oauth_scopes', ['site']);
    }

    /**
     * Finding #1: an access token issued before the user was deactivated kept working for the rest of
     * its life - an hour, or a day for tokens minted from an auth code.
     */
    public function testAccessTokenForDeactivatedUserIsRejected()
    {
        $this->bearerTokenValidator->shouldReceive('validateAuthorization')
            ->once()
            ->andReturn($this->validatedRequest('42'));

        // The account has since been deactivated, deleted, or flagged for a password reset.
        $this->userStatusValidator->shouldReceive('isValid')->once()->with('42')->andReturn(false);

        try {
            $this->validator->validateAuthorization(new ServerRequest('GET', 'https://example.com/api/1.0/system/info'));
            $this->fail('A token belonging to an inactive user was accepted.');
        } catch (OAuthServerException $e) {
            $this->assertSame('access_denied', $e->getErrorType());
            $this->assertSame(401, $e->getHttpStatusCode());
            $this->assertSame('Token is not linked to an active user', $e->getHint());
        }
    }

    /**
     * The check must not break the normal case.
     */
    public function testAccessTokenForActiveUserIsAccepted()
    {
        $validated = $this->validatedRequest('42');

        $this->bearerTokenValidator->shouldReceive('validateAuthorization')->once()->andReturn($validated);
        $this->userStatusValidator->shouldReceive('isValid')->once()->with('42')->andReturn(true);

        $result = $this->validator->validateAuthorization(
            new ServerRequest('GET', 'https://example.com/api/1.0/system/info')
        );

        $this->assertSame($validated, $result, 'The validated request must be returned untouched.');
        $this->assertSame('42', $result->getAttribute('oauth_user_id'));
        $this->assertSame('access-token-id', $result->getAttribute('oauth_access_token_id'));
        $this->assertSame(['site'], $result->getAttribute('oauth_scopes'));
    }

    /**
     * Tokens issued through the client credentials grant are not tied to a user at all and carry an
     * empty subject. They must keep working, and must not be run through the user check.
     */
    public function testClientCredentialsTokenIsUnaffected()
    {
        $validated = $this->validatedRequest('');

        $this->bearerTokenValidator->shouldReceive('validateAuthorization')->once()->andReturn($validated);
        $this->userStatusValidator->shouldNotReceive('isValid');

        $result = $this->validator->validateAuthorization(
            new ServerRequest('GET', 'https://example.com/api/1.0/system/info')
        );

        $this->assertSame($validated, $result, 'A client credentials token must still authenticate.');
    }

    /**
     * A token the bearer validator itself rejects (bad signature, expired, revoked) must keep failing
     * the way it always did, without the user check swallowing or masking the error.
     */
    public function testBearerTokenValidatorRejectionIsPropagated()
    {
        $this->bearerTokenValidator->shouldReceive('validateAuthorization')
            ->once()
            ->andThrow(OAuthServerException::accessDenied('Access token could not be verified'));

        $this->userStatusValidator->shouldNotReceive('isValid');

        $this->expectException(OAuthServerException::class);
        $this->validator->validateAuthorization(
            new ServerRequest('GET', 'https://example.com/api/1.0/system/info')
        );
    }
}
