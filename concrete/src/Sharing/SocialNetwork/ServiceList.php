<?php
namespace Concrete\Core\Sharing\SocialNetwork;

class ServiceList
{
    protected static function getServices()
    {
        $services = [
            ['facebook', 'Facebook', 'facebook'],
            ['twitter', 'Twitter', 'twitter'],
            ['instagram', 'Instagram', 'instagram'],
            ['tumblr', 'Tumblr', 'tumblr-square'],
            ['github', 'Github', 'github-square'],
            ['dribbble', 'Dribbble', 'dribbble'],
            ['pinterest', 'Pinterest', 'pinterest'],
            ['youtube', 'Youtube', 'youtube'],
            ['linkedin', 'LinkedIn', 'linkedin-square'],
            ['soundcloud', 'Soundcloud', 'soundcloud'],
            ['foursquare', 'Foursquare', 'foursquare'],
            ['flickr', 'Flickr', 'flickr'],
            ['googleplus', 'Google Plus', 'google-plus-square'],
            ['reddit', 'Reddit', 'reddit'],
            ['steam', 'Steam', 'steam'],
            ['twitch', 'Twitch', 'twitch'],
            ['vine', 'Vine', 'vine'],
            ['stumbleupon', 'Stumbleupon', 'stumbleupon'],
            ['skype', 'Skype', 'skype'],
            ['vk', 'Vkontakte', 'vk'],
            ['personal_website', 'Personal Website', 'external-link'],
            ['email', 'Email', 'envelope'],
            ['phone', 'Phone', 'phone-square'],
        ];

        // if additional social media services have been defined in custom config, append to built-in list or override
        if ($additionalSocialNetworks = \Config::get('concrete.social.additional_services')) {
            $serviceArray = [];
            $additionalKeyArray = [];

            // create arrays to merge using service handle as key, allows for overriding
            foreach ($services as $service) {
                $serviceArray[$service[0]] = $service;
            }
            foreach ($additionalSocialNetworks as $service) {
                $additionalKeyArray[$service[0]] = $service;
            }

            $services = array_values(array_merge($additionalKeyArray + $serviceArray));
        }

        return $services;
    }

    public static function get()
    {
        $services = static::getServices();
        $return = [];
        foreach ($services as $serviceArray) {
            $o = new Service($serviceArray[0], $serviceArray[1], $serviceArray[2], isset($serviceArray[3]) ? $serviceArray[3] : null);
            if ($o) {
                $return[] = $o;
            }
        }

        return $return;
    }
}
