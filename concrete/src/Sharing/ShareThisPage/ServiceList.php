<?php

namespace Concrete\Core\Sharing\ShareThisPage;

class ServiceList
{
    protected static function getServices()
    {
        return array(
            array('facebook', 'Facebook', 'fab fa-facebook'),
            array('twitter', 'X',  null, '<svg width="16" height="16" viewBox="0 0 300 300" version="1.1" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M178.57 127.15 290.27 0h-26.46l-97.03 110.38L89.34 0H0l117.13 166.93L0 300.25h26.46l102.4-116.59 81.8 116.59h89.34M36.01 19.54H76.66l187.13 262.13h-40.66"/></svg>'),
            array('linkedin', 'LinkedIn', 'fab fa-linkedin'),
            array('reddit', 'Reddit', 'fab fa-reddit'),
            array('pinterest', 'Pinterest', 'fab fa-pinterest'),
            array('google_plus', 'Google Plus', 'fab fa-google-plus-square'),
            ['bluesky', 'Bluesky', null, '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path fill="currentColor" d="M 3.47 1.91 C 5.30 3.28 7.28 6.07 8.00 7.57 C 8.72 6.07 10.70 3.28 12.53 1.91 C 13.86 0.91 16.00 0.14 16.00 2.59 C 16.00 3.08 15.72 6.69 15.55 7.28 C 14.98 9.32 12.90 9.84 11.05 9.53 C 14.29 10.08 15.11 11.90 13.33 13.73 C 9.96 17.19 8.48 12.86 8.10 11.75 C 8.03 11.54 8.00 11.45 8.00 11.53 C 8.00 11.45 7.97 11.54 7.90 11.75 C 7.52 12.86 6.04 17.19 2.67 13.73 C 0.89 11.90 1.71 10.08 4.95 9.53 C 3.10 9.84 1.02 9.32 0.45 7.28 C 0.28 6.69 0.00 3.08 0.00 2.59 C 0.00 0.14 2.14 0.91 3.47 1.91 Z M 3.47 1.91" /></svg>'],
            array('print', t('Print'), 'print', '<i class="fas fa-print" aria-hidden="true" title="' . h(t("Print")) . '"></i>'),
            array('email', 'Email', 'envelope', '<i class="fas fa-envelope" aria-hidden="true" title="' . h(t("Email")) . '"></i>'),
        );
    }

    public static function get()
    {
        $services = [];

        foreach (static::getServices() as $serviceArray) {
            $ssHandle = isset($serviceArray[0]) ? $serviceArray[0] : null;
            $ssName = isset($serviceArray[1]) ? $serviceArray[1] : null;
            $ssIcon = isset($serviceArray[2]) ? $serviceArray[2] : null;
            $customHTML = isset($serviceArray[3]) ? $serviceArray[3] : null;

            $service = new Service($ssHandle, $ssName, $ssIcon, $customHTML);

            $services[] = $service;
        }

        return $services;
    }
}
