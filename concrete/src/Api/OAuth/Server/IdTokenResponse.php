<?php

namespace Concrete\Core\Api\OAuth\Server;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Site\Service;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Lcobucci\JWT\{
    Builder,
    Encoding\ChainedFormatter,
    Encoding\JoseEncoder,
    Token,
    Token\Builder as TokenBuilder,
    Token\RegisteredClaims
};
use Lcobucci\JWT\Signer\{
    Key,
    Key\InMemory,
    Rsa\Sha256
};
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse;
use League\OpenIdConnectClaims\ClaimsSet;

class IdTokenResponse extends BearerTokenResponse
{

    /**
     * The site service we use to determine the issuer
     *
     * @var \Concrete\Core\Site\Service
     */
    protected $site;

    /**
     * A factory for building claim sets
     *
     * @var \Concrete\Core\Api\OAuth\Server\ClaimsSetFactory
     */
    protected $claimFactory;

    /**
     * A repository that we can get the active user from
     *
     * @var \Concrete\Core\User\UserInfoRepository
     */
    protected $userInfoRepository;

    public function __construct(Service $site, ClaimsSetFactory $claimFactory, UserInfoRepository $userInfoRepository)
    {
        $this->site = $site;
        $this->claimFactory = $claimFactory;
        $this->userInfoRepository = $userInfoRepository;
    }

    /**
     * Get the extra params to include
     * If this is an OIDC request we include the ID token
     *
     * @param \League\OAuth2\Server\Entities\AccessTokenEntityInterface $accessToken
     *
     * @return array
     */
    protected function getExtraParams(AccessTokenEntityInterface $accessToken)
    {
        $params = parent::getExtraParams($accessToken);

        // If this is an OIDC request, pack a new id token into it
        if ($this->isOidcRequest($accessToken->getScopes())) {
            $user = $this->userInfoRepository->getByID($accessToken->getUserIdentifier());

            if ($user) {
                $params['id_token'] = (string) $this->createIdToken($accessToken, $this->claimFactory->createFromUserInfo($user));
            }
        }

        return $params;
    }

    /**
     * Create an ID token to include with our response
     *
     * @param \League\OAuth2\Server\Entities\AccessTokenEntityInterface $accessToken
     * @param \League\OpenIdConnectClaims\ClaimsSet $claims
     *
     * @return \Lcobucci\JWT\Token
     */
    protected function createIdToken(AccessTokenEntityInterface $accessToken, ClaimsSet $claims)
    {
        $issuer = $this->site->getSite();

        // Initialize the builder
        $now = new \DateTimeImmutable();

        if (class_exists(TokenBuilder::class)) {
            $builder = new TokenBuilder(new JoseEncoder(), ChainedFormatter::default());
        } else {
            $builder = new Builder();
        }

        $builder = $builder
            ->permittedFor($accessToken->getClient()->getIdentifier())
            ->issuedBy($issuer ? $issuer->getSiteCanonicalURL() : 'concrete5')
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($accessToken->getExpiryDateTime())
            ->relatedTo($accessToken->getUserIdentifier());

        // Apply claims
        foreach ($claims->jsonSerialize() as $key => $claim) {
            // Avoid calling `withClaim` for registered claims
            switch ($key) {
                case RegisteredClaims::ID:
                    $builder = $builder->identifiedBy($claim);
                    break;
                case RegisteredClaims::EXPIRATION_TIME:
                    $builder = $builder->expiresAt($claim);
                    break;
                case RegisteredClaims::NOT_BEFORE:
                    $builder = $builder->canOnlyBeUsedAfter($claim);
                    break;
                case RegisteredClaims::ISSUED_AT:
                    $builder = $builder->issuedAt($claim);
                    break;
                case RegisteredClaims::ISSUER:
                    $builder = $builder->issuedBy($claim);
                    break;
                case RegisteredClaims::AUDIENCE:
                    $builder = $builder->permittedFor($claim);
                    break;
                case RegisteredClaims::SUBJECT:
                    $builder = $builder->relatedTo($claim);
                    break;
                default:
                    $builder = $builder->withClaim($key, $claim);
                    break;
            }
        }

        // Return the newly signed token
        if (class_exists(InMemory::class)) {
            $key = InMemory::plainText($this->privateKey->getKeyContents(), (string) $this->privateKey->getPassPhrase());
        } else {
            $key = new Key($this->privateKey->getKeyContents(), $this->privateKey->getPassPhrase());
        }

        $token = $builder->getToken(new Sha256(), $key);

        if ($token instanceof Token) {
            return $token->toString();
        }

        return $token;
    }

    /**
     * Determine if this request is an OIDC requadmin   past. We do this by checking if the "openid" scope is included
     *
     * @param \League\OAuth2\Server\Entities\ScopeEntityInterface[] $scopes
     *
     * @return bool
     */
    protected function isOidcRequest(array $scopes)
    {
        foreach ($scopes as $scope) {
            if ($scope->getIdentifier() === 'openid') {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the application object.
     *
     * @param \Concrete\Core\Application\Application $application
     */
    public function setApplication(Application $application)
    {
        // TODO: Implement setApplication() method.
    }
}
