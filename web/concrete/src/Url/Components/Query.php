<?php
namespace Concrete\Core\Url\Components;

/**
 * c5 specific query component for league/query
 */
class Query extends \League\Url\Components\Query
{
    /**
     * {@inheritdoc}
     * Why we override this method? See: {@link  https://github.com/concrete5/concrete5/pull/2626}
     */
    public function get()
    {
        if (!$this->data) {
            return null;
        }
        
        $pairs = array();
        foreach ($this->data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $val) {
                    $pairs[] = rawurlencode($key.'[]').'='.rawurlencode($val);
                }
            } else {
                $pairs[] = rawurlencode($key).'='.rawurlencode($value);
            }
        }
        return implode('&', $pairs);
    }
}
