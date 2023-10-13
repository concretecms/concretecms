<?php

namespace Concrete\Core\Twig;

use Concrete\Core\Http\Request;
use Concrete\Core\Url\UrlImmutable;
use Pagerfanta\RouteGenerator\RouteGeneratorInterface;

class TwigRouteGenerator implements RouteGeneratorInterface
{
    /**
     * @var array $options
     */
    private $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function __invoke(int $page): string
    {
        $request = Request::getInstance()->server->all();
        $url = UrlImmutable::createFromServer($request);
        $query = $url->getQuery()->toArray() ?: [];

        // Set the page query parameter to the current page number.
        $query['page'] = $page;

        // Merge any additional query parameters specified in the options.
        $query = array_merge($query, array_key_exists('query', $this->options) ? $this->options['query'] : []);

        // Remove any query parameters with a null value.
        $query = array_filter($query, function ($value) {
            return $value !== null;
        });

        return (string) $url->setQuery(http_build_query($query));
    }
}
