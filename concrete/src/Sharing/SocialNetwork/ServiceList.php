<?php
namespace Concrete\Core\Sharing\SocialNetwork;

class ServiceList
{
    protected static function getServices()
    {
        $services = [
            ['facebook', 'Facebook', 'fab fa-facebook'],
            ['twitter', 'Twitter', 'fab fa-twitter'],
            ['instagram', 'Instagram', 'fab fa-instagram'],
            ['tumblr', 'Tumblr', 'fab fa-tumblr-square'],
            ['github', 'Github', 'fab fa-github-square'],
            ['dribbble', 'Dribbble', 'fab fa-dribbble'],
            ['pinterest', 'Pinterest', 'fab fa-pinterest'],
            ['youtube', 'Youtube', 'fab fa-youtube'],
            ['linkedin', 'LinkedIn', 'fab fa-linkedin'],
            ['soundcloud', 'Soundcloud', 'fab fa-soundcloud'],
            ['foursquare', 'Foursquare', 'fab fa-foursquare'],
            ['flickr', 'Flickr', 'fab fa-flickr'],
            ['googleplus', 'Google Plus', 'fab fa-google-plus-square'],
            ['reddit', 'Reddit', 'fab fa-reddit'],
            ['steam', 'Steam', 'fab fa-steam'],
            ['twitch', 'Twitch', 'fab fa-twitch'],
            ['vine', 'Vine', 'fab fa-vine'],
            ['stumbleupon', 'Stumbleupon', 'fab fa-stumbleupon'],
            ['skype', 'Skype', 'fab fa-skype'],
            ['vk', 'Vkontakte', 'fab fa-vk'],
            ['personal_website', 'Personal Website', 'fa fa-external-link-alt'],
            ['email', 'Email', 'fa fa-envelope'],
            ['phone', 'Phone', 'fa fa-phone-square'],
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
