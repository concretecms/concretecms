<?php
namespace Concrete\Core\Url;

use RuntimeException;

class Url extends \League\Url\Url implements UrlInterface
{

    /**
     * @param integer $port
     * @deprecated Use `->setPort($port)`
     * @return UrlInterface
     */
    public function setPortIfNecessary($port)
    {
        return $this->setPort($port);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthority()
    {
        $user = $this->getUserInfo();
        $host = $this->host->getUriComponent();
        $port = $this->port->getUriComponent();

        $authority = $user . $host;
        $scheme = strtolower($this->getScheme());
        $port_map = array(
            'http' => ':80',
            'https' => ':443'
        );

        if (!isset($port_map[$scheme]) || $port_map[$scheme] != $port) {
            $authority .= $port;
        }

        return $authority;
    }

    public static function createFromUrl($url, $trailing_slashes = self::TRAILING_SLASHES_AUTO)
    {
        if ($trailing_slashes === self::TRAILING_SLASHES_AUTO) {
            $trailing_slashes = (bool) \Config::get('concrete.seo.trailing_slash', false);
        }
        $trailing_slashes = (bool) $trailing_slashes;

        $url = (string) $url;
        $url = trim($url);
        $original_url = $url;
        $url = self::sanitizeUrl($url);

        //if no valid scheme is found we add one
        if (is_null($url)) {
            throw new RuntimeException(
                sprintf(
                    'The given URL: `%s` could not be parsed',
                    $original_url));
        }
        $components = @parse_url($url);
        if (false === $components) {
            throw new RuntimeException(
                sprintf(
                    'The given URL: `%s` could not be parsed',
                    $original_url));
        }

        $components = array_merge(
            array(
                'scheme' => null,
                'user' => null,
                'pass' => null,
                'host' => null,
                'port' => null,
                'path' => null,
                'query' => null,
                'fragment' => null,
            ),
            $components);
        $components = self::formatAuthComponent($components);
        $components = self::formatPathComponent($components, $original_url);

        return new static(
            new      \League\Url\Components\Scheme($components['scheme']),
            new        \League\Url\Components\User($components['user']),
            new        \League\Url\Components\Pass($components['pass']),
            new        \League\Url\Components\Host($components['host']),
            new        \League\Url\Components\Port($components['port']),
            new \Concrete\Core\Url\Components\Path($components['path'], $trailing_slashes),
            new       \League\Url\Components\Query($components['query']),
            new    \League\Url\Components\Fragment($components['fragment'])
        );
    }

    /**
     * Overridden to allow paths be passed in and out
     * @param $url
     * @return null|string
     */
    protected static function sanitizeUrl($url)
    {
        if (strpos($url, '/') === 0) {
            return $url;
        }
        return parent::sanitizeUrl($url);
    }

}
