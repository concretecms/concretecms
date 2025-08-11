<?php
namespace Concrete\Core\Sharing\SocialNetwork;

class ServiceList
{
    protected static function getServices()
    {
        $services = [
            ['facebook', 'Facebook', 'fab fa-facebook'],
            ['twitter', 'X',  null, '<svg width="16" height="16" viewBox="0 0 300 300" version="1.1" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M178.57 127.15 290.27 0h-26.46l-97.03 110.38L89.34 0H0l117.13 166.93L0 300.25h26.46l102.4-116.59 81.8 116.59h89.34M36.01 19.54H76.66l187.13 262.13h-40.66"/></svg>'],
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
            ['discord', 'Discord', 'fab fa-discord'],
            ['personal_website', 'Personal Website', 'fa fa-external-link-alt'],
            ['email', 'Email', 'fa fa-envelope'],
            ['phone', 'Phone', 'fa fa-phone-square'],
            ['bluesky', 'Bluesky',  null, '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path fill="currentColor" d="M 3.47 1.91 C 5.30 3.28 7.28 6.07 8.00 7.57 C 8.72 6.07 10.70 3.28 12.53 1.91 C 13.86 0.91 16.00 0.14 16.00 2.59 C 16.00 3.08 15.72 6.69 15.55 7.28 C 14.98 9.32 12.90 9.84 11.05 9.53 C 14.29 10.08 15.11 11.90 13.33 13.73 C 9.96 17.19 8.48 12.86 8.10 11.75 C 8.03 11.54 8.00 11.45 8.00 11.53 C 8.00 11.45 7.97 11.54 7.90 11.75 C 7.52 12.86 6.04 17.19 2.67 13.73 C 0.89 11.90 1.71 10.08 4.95 9.53 C 3.10 9.84 1.02 9.32 0.45 7.28 C 0.28 6.69 0.00 3.08 0.00 2.59 C 0.00 0.14 2.14 0.91 3.47 1.91 Z M 3.47 1.91" /></svg>'],
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
