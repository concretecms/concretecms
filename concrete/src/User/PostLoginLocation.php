<?php

namespace Concrete\Core\User;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Desktop\DesktopList;
use Concrete\Core\Page\Page;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class to handle the URL to redirect users to upon login.
 */
class PostLoginLocation
{
    /**
     * The session key to be used to store the post-login URL.
     *
     * @var string
     */
    const POSTLOGIN_SESSION_KEY = 'ccm-post-login-url';

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    protected $session;

    /**
     * @var \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface
     */
    protected $resolverManager;

    /**
     * @var \Concrete\Core\Http\ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * @var \Concrete\Core\Utility\Service\Validation\Numbers
     */
    protected $valn;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Config\Repository\Repository $config
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     * @param \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $resolverManager
     * @param \Concrete\Core\Utility\Service\Validation\Numbers $valn
     * @param \Concrete\Core\Http\ResponseFactoryInterface $responseFactory
     */
    public function __construct(Repository $config, Session $session, ResolverManagerInterface $resolverManager, ResponseFactoryInterface $responseFactory, Numbers $valn)
    {
        $this->config = $config;
        $this->session = $session;
        $this->resolverManager = $resolverManager;
        $this->responseFactory = $responseFactory;
        $this->valn = $valn;
    }

    /**
     * Clear the post-login data saved in session.
     */
    public function resetSessionPostLoginUrl()
    {
        $this->session->remove(static::POSTLOGIN_SESSION_KEY);
    }

    /**
     * Store in the session the post-login URL or page.
     *
     * @param string|\League\URL\URLInterface|\Concrete\Core\Page\Page|int $url the URL to redirect users to after login
     */
    public function setSessionPostLoginUrl($url)
    {
        $normalized = null;
        if ($url instanceof Page) {
            if (!$url->isError()) {
                $cID = (int) $url->getCollectionID();
                if ($cID > 0) {
                    $normalized = $cID;
                }
            }
        } elseif ($this->valn->integer($url, 1)) {
            $normalized = (int) $url;
        } else {
            $url = (string) $url;
            if (strpos($url, '/') !== false) {
                $normalized = $url;
            }
        }
        $this->resetSessionPostLoginUrl();
        if ($normalized !== null) {
            $this->session->set(static::POSTLOGIN_SESSION_KEY, $normalized);
        }
    }

    /**
     * Get the post-login URL as stored in the session.
     *
     * @param bool $resetSessionPostLoginUrl Should the post-login-related data stored in session be cleared?
     *
     * @return string Empty string if unavailable
     */
    public function getSessionPostLoginUrl($resetSessionPostLoginUrl = false)
    {
        $result = '';
        $normalized = $this->session->get(static::POSTLOGIN_SESSION_KEY);
        if ($this->valn->integer($normalized, 1)) {
            $page = Page::getByID($normalized);
            if ($page && !$page->isError()) {
                $result = (string) $this->resolverManager->resolve([$page]);
            }
        } elseif (strpos((string) $normalized, '/') !== false) {
            $result = $normalized;
        }

        if ($resetSessionPostLoginUrl) {
            $this->resetSessionPostLoginUrl();
        }

        return $result;
    }

    /**
     * Get the default post-login URL.
     *
     * @return string Empty string if unavailable
     */
    public function getDefaultPostLoginUrl()
    {
        $result = '';
        $loginRedirectMode = (string) $this->config->get('concrete.misc.login_redirect');
        switch ($loginRedirectMode) {
            case 'CUSTOM':
                $loginRedirectCollectionID = $this->config->get('concrete.misc.login_redirect_cid');
                if ($this->valn->integer($loginRedirectCollectionID, 1)) {
                    $page = Page::getByID($loginRedirectCollectionID);
                    if ($page && !$page->isError()) {
                        $result = (string) $this->resolverManager->resolve([$page]);
                    }
                }
                break;
            case 'DESKTOP':
                $desktop = DesktopList::getMyDesktop();
                if ($desktop && !$desktop->isError()) {
                    $result = (string) $this->resolverManager->resolve([$desktop]);
                }
                break;
        }

        return $result;
    }

    /**
     * Get the fallback post-login URL (called when all other methods fail).
     *
     * @return string
     */
    public function getFallbackPostLoginUrl()
    {
        $homeCid = Page::getHomePageID();
        $home = $homeCid ? Page::getByID($homeCid) : null;
        if ($home && !$home->isError()) {
            $result = (string) $this->resolverManager->resolve([$home]);
        } else {
            $result = (string) $this->resolverManager->resolve(['/']);
        }

        return $result;
    }

    /**
     * Get the post-login URL.
     *
     * @param bool $resetSessionPostLoginUrl Should the post-login-related data stored in session be cleared?
     *
     * @return string
     */
    public function getPostLoginUrl($resetSessionPostLoginUrl = false)
    {
        $result = $this->getSessionPostLoginUrl($resetSessionPostLoginUrl);
        if ($result === '') {
            $result = $this->getDefaultPostLoginUrl();
            if ($result === '') {
                $result = $this->getFallbackPostLoginUrl();
            }
        }

        return $result;
    }

    /**
     * Create a Response that redirects the user to the configured URL.
     *
     * @param bool $resetSessionPostLoginUrl Should the post-login-related data stored in session be cleared?
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPostLoginRedirectResponse($resetSessionPostLoginUrl = false)
    {
        $result = $this->responseFactory->redirect(
            $this->getPostLoginUrl($resetSessionPostLoginUrl),
            Response::HTTP_FOUND
        );
        // Disable caching for response
        $result = $result->setMaxAge(0)->setSharedMaxAge(0)->setPrivate();
        $result->headers->addCacheControlDirective('must-revalidate', true);
        $result->headers->addCacheControlDirective('no-store', true);

        return $result;
    }
}
