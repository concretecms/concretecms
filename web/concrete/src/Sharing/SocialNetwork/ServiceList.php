<?php
namespace Concrete\Core\Sharing\SocialNetwork;

class ServiceList
{
    protected static function getServices()
    {
        $services = array(
            array('facebook', 'Facebook', 'facebook'),
            array('twitter', 'Twitter', 'twitter'),
            array('instagram', 'Instagram', 'instagram'),
            array('tumblr', 'Tumblr', 'tumblr-square'),
            array('github', 'Github', 'github-square'),
            array('dribbble', 'Dribbble', 'dribbble'),
            array('pinterest', 'Pinterest', 'pinterest'),
            array('youtube', 'Youtube', 'youtube'),
            array('linkedin', 'LinkedIn', 'linkedin-square'),
            array('soundcloud', 'Soundcloud', 'soundcloud'),
            array('foursquare', 'Foursquare', 'foursquare'),
            array('flickr', 'Flickr', 'flickr'),
            array('googleplus', 'Google Plus', 'google-plus-square'),
            array('reddit', 'Reddit', 'reddit'),
            array('steam', 'Steam', 'steam'),
            array('vine', 'Vine', 'vine'),
            array('stumbleupon', 'Stumbleupon', 'stumbleupon'),
            array('skype', 'Skype', 'skype'),
            array('personal_website', 'Personal Website', 'external-link'),
        );

        if ($additionalSocialNetworks = \Config::get('concrete.social.additional_services')) {
            $services = array_merge($services, $additionalSocialNetworks);
        }

        return $services;
    }

    public static function get()
    {
        $services = static::getServices();
        $return = array();
        foreach ($services as $serviceArray) {
            $o = new Service($serviceArray[0], $serviceArray[1], $serviceArray[2], isset($serviceArray[3]) ? $serviceArray[3] : null);
            $return[] = $o;
        }

        return $return;
    }
}
