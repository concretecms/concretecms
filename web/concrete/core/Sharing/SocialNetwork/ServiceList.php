<?php
namespace Concrete\Core\Sharing\SocialNetwork;

class ServiceList
{
    protected static function getServices()
    {
        return array(
            array(1, 'Facebook', 'facebook'),
            array(2, 'Twitter', 'twitter'),
            array(3, 'Flickr', 'flickr'),
            array(4, 'Tumblr', 'tumblr-square'),
            array(5, 'Github', 'github-square'),
            array(6, 'Dribbble', 'dribbble'),
            array(7, 'Pinterest', 'pinterest'),
            array(8, 'Youtube', 'youtube'),
            array(9, 'LinkedIn', 'linkedin-square'),
            array(10, 'Soundcloud', 'soundcloud'),
            array(11, 'Foursquare', 'foursquare'),
            array(12, 'Google Plus', 'google-plus-square'),
            array(13, 'Reddit', 'reddit'),
            array(14, 'Steam', 'steam'),
            array(15, 'Vine', 'vine'),
            array(16, 'Stumbleupon', 'stumbleupon'),
            array(17, 'Skype', 'skype')
        );
    }

    public static function get()
    {
        $services = static::getServices();
        $return = array();
        foreach($services as $serviceArray)
        {
            $o = new Service($serviceArray[0], $serviceArray[1], $serviceArray[2]);
            $return[] = $o;
        }
        return $return;
    }


}