<?php

namespace Concrete\Core\Utility\Service;

/**
 * A Utility class for manipulating HTML
 */
class Html {

    /**
     * Takes in a string, and adds rel="nofollow" to any a tags that contain an href attribute
     * @param string $input
     * @return string
     */
    public function noFollowHref($input)
    {
        return preg_replace_callback(
            '/(?:<a(.*?href.*?)>)/i',
            function ($matches) {
                if (strpos($matches[1], 'rel="nofollow"') === false) {
                    //if there is no nofollow add it
                    return '<a' . $matches[1] . ' rel="nofollow">';
                } else {
                    //if there is already a nofollow take no action
                    return $matches[0];
                }
            },
            $input
        );
    }

} 