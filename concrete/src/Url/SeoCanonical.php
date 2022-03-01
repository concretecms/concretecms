<?php
namespace Concrete\Core\Url;

use Concrete\Core\Page\Page;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Utility\Service\Validation\Numbers;
use League\Url\Components\Query;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Helper class to work with SEO canonical URLs.
 */
class SeoCanonical
{
    /**
     * The instance of the class that builds page URLs.
     *
     * @var ResolverManagerInterface
     */
    protected $resolver;

    /**
     * The instance of the numbers validator.
     *
     * @var Numbers
     */
    protected $valn;

    /**
     * The list of query string parameters to be included from generated canonical URLs.
     *
     * @var string[]|\Traversable
     */
    protected $includedQuerystringParameters;

    /**
     * The list of path arguments to be included in canonical URLs.
     *
     * @var string[]
     */
    protected $pathArguments = [];

    /**
     * Initialize the instance.
     *
     * @param ResolverManagerInterface $resolver the instance of the class that builds page URLs
     * @param Numbers $valn the instance of the numbers validator
     * @param string[]|\Traversable $includedQuerystringParameters the list of query string parameters to be included from generated canonical URLs
     */
    public function __construct(ResolverManagerInterface $resolver, Numbers $valn, $includedQuerystringParameters)
    {
        $this->resolver = $resolver;
        $this->valn = $valn;
        $this->includedQuerystringParameters = $includedQuerystringParameters ?: [];
    }

    /**
     * Generate the canonical URL of a page.
     *
     * @param Page|int $page The Page instance (or its collection ID)
     * @param Request|ParameterBag|Query|array|string|null $querystring Optional query string parameters
     *
     * @return \League\URL\URLInterface|null
     */
    public function getPageCanonicalURL($page, $querystring = null)
    {
        $result = null;
        if ($page) {
            if ($this->valn->integer($page, 1)) {
                $page = Page::getByID($page);
            }
            if ($page instanceof Page && !$page->isError()) {
                $cID = $page->getCollectionID();
                $originalCID = $page->getCollectionPointerOriginalID();
                if (!empty($originalCID) && $originalCID != $cID) {
                    $result = $this->getPageCanonicalURL($cID, $querystring);
                } else {
                    $args = [$page];
                    if ($pathArguments = $this->getPathArguments()) {
                        $args = array_merge($args, $pathArguments);
                    }
                    $result = $this->resolver->resolve($args);
                    $query = null;
                    if ($querystring instanceof Query) {
                        $query = clone $querystring;
                    } elseif ($querystring instanceof Request) {
                        $query = new Query($querystring->query->all());
                    } elseif ($querystring instanceof ParameterBag) {
                        $query = new Query($querystring->all());
                    } elseif (is_array($querystring)) {
                        $query = new Query($querystring);
                    } elseif (is_string($querystring)) {
                        if ($querystring !== '') {
                            $query = new Query($querystring);
                        }
                    }
                    if ($query !== null && $query->count() > 0) {
                        foreach ($query as $key => $value) {
                            if (!in_array($key, $this->includedQuerystringParameters)) {
                                $query->offsetUnset($key);
                            }
                        }
                        $result = $result->setQuery($query);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Generate the <meta rel="canonical" href="..."> tag of a page.
     *
     * @param Page|int $page The Page instance (or its collection ID)
     * @param Request|ParameterBag|Query|array|string|null $querystring Optional query string parameters
     *
     * @return \HtmlObject\Element|null
     */
    public function getPageCanonicalURLTag($page, $querystring = null)
    {
        $result = null;
        $url = $this->getPageCanonicalURL($page, $querystring);
        if ($url !== null) {
            $result = new \HtmlObject\Element(
                'link',
                null,
                [
                    'rel' => 'canonical',
                    'href' => $url,
                ]
            );
            $result->setIsSelfClosing(true);
        }

        return $result;
    }

    /**
     * Get path arguments to append canonical URLs
     *
     * @return string[]|null
     */
    public function getPathArguments()
    {
        return $this->pathArguments;
    }

    /**
     * Set path arguments to append canonical URLs
     *
     * @param string[] $pathArguments
     */
    public function setPathArguments($pathArguments)
    {
        $this->pathArguments = $pathArguments;
    }

    /**
     * @since 9.0.3
     * @param string $parameter
     * @return void
     */
    public function addIncludedQuerystringParameter(string $parameter)
    {
        $this->includedQuerystringParameters[] = $parameter;
    }
}
