<?php
namespace Concrete\Core\Url\Resolver;

use Concrete\Core\Url\Url;

class PathUrlResolver implements UrlResolverInterface
{

    /**
     * {@inheritdoc}
     */
    public function resolve(array $arguments, $resolved = null)
    {
        if ($resolved) {
            // We don't need to do any post processing on urls.
            return $resolved;
        }

        $args = $arguments;
        $path = array_shift($args);
        $trailing = \Config::get('concrete.seo.trailing_slash');

        if (is_scalar($path) || (is_object($path) &&
                method_exists($path, '__toString'))
        ) {
            $path = rtrim($path, '/');
            if ($path === '') {
                $path = '/'; // This is in the case of the home page, which has no cPath
            }

            $url = Url::createFromUrl('', $trailing);
            $this->handleHost($url, $path, $args);
            $this->handlePath($url, $path, $args);
            $this->handleDispatcher($url, $path, $args);

            return $url;
        }

        return null;
    }

    public function handlePath(Url $url, $path, $args)
    {
        $components = parse_url($path);
        if ($string = array_get($components, 'path')) {
            $url->getPath()->set($string);
        }
        if ($string = array_get($components, 'query')) {
            $url->getQuery()->set($string);
        }
        if ($string = array_get($components, 'fragment')) {
            $url->getFragment()->set($string);
        }

        if (\Core::getApplicationRelativePath()) {
            $url->getPath()->prepend(\Core::getApplicationRelativePath());
        }

        foreach ($args as $segment) {
            if (!is_array($segment)) {
                $segment = (string) $segment; // sometimes integers foul this up when we pass them in as URL arguments.
            }
            $url->getPath()->append($segment);
        }
    }

    public function handleDispatcher(Url $url, $path, $args)
    {
        $rewriting    = \Config::get('concrete.seo.url_rewriting');
        $rewrite_all  = \Config::get('concrete.seo.url_rewriting_all');
        $in_dashboard = \Core::make('helper/concrete/dashboard')->inDashboard($path);

        // If rewriting is disabled, or all_rewriting is disabled and we're
        // in the dashboard, add the dispatcher.
        if (!$rewriting || (!$rewrite_all && $in_dashboard)) {
            $url->getPath()->prepend(DISPATCHER_FILENAME);
        }
    }

    public function handleHost(Url $url, $path, $args)
    {
        // Normalize
        $url->setHost(null);
        $url->setScheme(null);

        if (\Config::get('concrete.seo.canonical_host')) {
            $url->getHost()->set(\Config::get('concrete.seo.canonical_host'));
        } else {
            $url->getHost()->set(\Request::getInstance()->getHost());
        }

        if ($url->getHost()->get()) {
            $url->setScheme('http');
        }
    }

}
