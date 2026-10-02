<?php
namespace Concrete\Core\Url\Components;

/**
 * c5 specific path component for league/url.
 *
 * league/url documents AbstractArray::offsetGet() as returning only null: this is its actual signature.
 *
 * @method string|null offsetGet(int|string $offset)
 */
class Path extends \League\Url\Components\Path
{
    protected $trail = false;

    /**
     * @param bool                        $trailing_slash
     */
    public function __construct($data, $trailing_slash = false)
    {
        $this->set($data);
        $this->trail = (bool) $trailing_slash;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $res = array();
        foreach (array_values($this->data) as $value) {
            $res[] = rawurlencode($value);
        }
        if (!$res) {
            return null;
        }

        foreach ($res as $key => $value) {
            if ($value === '') {
                unset($res[$key]);
            }
        }

        return implode($this->delimiter, $res) . (((bool) $this->trail && $res) ? '/' : '');
    }

    public function withoutDispatcher()
    {
        $path = clone $this;
        if ($path[0] == DISPATCHER_FILENAME) {
            $path->offsetUnset(0);
        }

        return $path;
    }
}
