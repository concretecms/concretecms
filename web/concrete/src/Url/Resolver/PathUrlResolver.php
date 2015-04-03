<?php
namespace Concrete\Core\Url\Resolver;

use Concrete\Core\Url\Components\Path;
use Concrete\Core\Url\UrlInterface;

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

        if (is_scalar($path) || (is_object($path) &&
                method_exists($path, '__toString'))
        ) {
            $path = rtrim($path, '/');

            $url = \Core::make('url/canonical');
            $url = $this->handlePath($url, $path, $args);

            return $url;
        }

        return null;
    }

    public function handlePath(UrlInterface $url, $path, $args)
    {
        $path_object = $this->basePath($url, $path, $args);

        $components = parse_url($path);
        if ($string = array_get($components, 'path')) {
            $path_object->append($string);
        }
        if ($string = array_get($components, 'query')) {
            $url = $url->setQuery($string);
        }
        if ($string = array_get($components, 'fragment')) {
            $url = $url->setFragment($string);
        }

        foreach ($args as $segment) {
            if (!is_array($segment)) {
                $segment = (string) $segment; // sometimes integers foul this up when we pass them in as URL arguments.
            }
            $path_object->append($segment);
        }

        $url_path = $url->getPath();
        $url_path->append($path_object);

        return $url->setPath($url_path);
    }

    public function basePath($url, $path, $args)
    {
        $path_object = new Path('');

        $rewriting    = \Config::get('concrete.seo.url_rewriting');
        $rewrite_all  = \Config::get('concrete.seo.url_rewriting_all');
        $in_dashboard = \Core::make('helper/concrete/dashboard')->inDashboard($path);

        // If rewriting is disabled, or all_rewriting is disabled and we're
        // in the dashboard, add the dispatcher.
        if (!$rewriting || (!$rewrite_all && $in_dashboard)) {
            $path_object->prepend(DISPATCHER_FILENAME);
        }

        return $path_object;
    }

}
